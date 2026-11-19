<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\AutoDmRule;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoDmRuleModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_and_persist_auto_dm_rule(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '123456789',
            'name' => 'Test Page',
            'access_token' => 'test-token',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Ebook Download DM',
            'keywords' => ['ebook', 'pdf', 'guide'],
            'match_type' => AutoDmRule::MATCH_CONTAINS,
            'dm_message' => 'Hey! Here is your download link: https://example.com/ebook.pdf',
            'public_reply' => 'Sent to your DM! 📥',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('auto_dm_rules', [
            'id' => $rule->id,
            'name' => 'Ebook Download DM',
            'match_type' => 'contains',
            'is_active' => 1,
            'trigger_count' => 0,
        ]);

        $this->assertIsArray($rule->keywords);
        $this->assertEquals(['ebook', 'pdf', 'guide'], $rule->keywords);
        $this->assertTrue($rule->user->is($user));
        $this->assertTrue($rule->account->is($account));
        $this->assertNull($rule->post_id);
    }

    public function test_matches_text_contains_mode(): void
    {
        $rule = new AutoDmRule([
            'keywords' => ['link', 'price', 'buy'],
            'match_type' => AutoDmRule::MATCH_CONTAINS,
        ]);

        $this->assertTrue($rule->matchesText('Can you send the link please?'));
        $this->assertTrue($rule->matchesText('LINK'));
        $this->assertTrue($rule->matchesText('What is the price for this?'));
        $this->assertTrue($rule->matchesText('I want to buy now!'));
        $this->assertFalse($rule->matchesText('Great photo, loved it!'));
    }

    public function test_matches_text_exact_mode(): void
    {
        $rule = new AutoDmRule([
            'keywords' => ['link', 'price'],
            'match_type' => AutoDmRule::MATCH_EXACT,
        ]);

        $this->assertTrue($rule->matchesText('Link'));
        $this->assertTrue($rule->matchesText(' LINK '));
        $this->assertTrue($rule->matchesText('price'));
        $this->assertFalse($rule->matchesText('Send me link'));
        $this->assertFalse($rule->matchesText('What is the price?'));
    }

    public function test_matches_text_empty_keywords(): void
    {
        $rule = new AutoDmRule(['keywords' => []]);
        $this->assertFalse($rule->matchesText('anything'));

        $rule2 = new AutoDmRule(['keywords' => null]);
        $this->assertFalse($rule2->matchesText('anything'));
    }

    public function test_record_trigger_increments_and_sets_timestamp(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_INSTAGRAM,
            'meta_account_id' => '987654321',
            'name' => 'Test Insta',
            'access_token' => 'test-token',
        ]);

        $rule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Pricing Bot',
            'keywords' => ['price'],
            'match_type' => AutoDmRule::MATCH_CONTAINS,
            'dm_message' => 'Check pricing here',
            'is_active' => true,
        ]);

        $this->assertEquals(0, $rule->trigger_count);
        $this->assertNull($rule->last_triggered_at);

        $rule->recordTrigger();
        $rule->refresh();

        $this->assertEquals(1, $rule->trigger_count);
        $this->assertNotNull($rule->last_triggered_at);
    }

    public function test_scope_active(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_FACEBOOK,
            'meta_account_id' => '555',
            'name' => 'Active Test',
            'access_token' => 'test-token',
        ]);

        $activeRule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Active',
            'keywords' => ['active'],
            'dm_message' => 'Active msg',
            'is_active' => true,
        ]);

        $inactiveRule = AutoDmRule::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'name' => 'Inactive',
            'keywords' => ['paused'],
            'dm_message' => 'Inactive msg',
            'is_active' => false,
        ]);

        $activeRules = AutoDmRule::active()->get();

        $this->assertTrue($activeRules->contains($activeRule));
        $this->assertFalse($activeRules->contains($inactiveRule));
    }
}
