@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('user-role-select');
    const clientTypeWrapper = document.getElementById('client-type-wrapper');
    const clientTypeSelect = document.getElementById('client-type-select');
    const planFeaturesSection = document.getElementById('plan-features-section');
    const managerPermissionsSection = document.getElementById('manager-permissions-section');

    if (!roleSelect) return;

    const ROLE_USER = '{{ \App\Models\User::ROLE_USER }}';
    const ROLE_MANAGER = '{{ \App\Models\User::ROLE_MANAGER }}';

    function toggleSectionInputs(sectionEl, enabled) {
        if (!sectionEl) return;
        sectionEl.querySelectorAll('input, select, textarea').forEach(function (el) {
            el.disabled = !enabled;
        });
    }

    function syncRoleFields() {
        const selectedRole = String(roleSelect.value);
        const isUser = selectedRole === ROLE_USER;
        const isManager = selectedRole === ROLE_MANAGER;

        if (clientTypeWrapper && clientTypeSelect) {
            clientTypeWrapper.classList.toggle('d-none', !isUser);
            clientTypeSelect.required = isUser;
            clientTypeSelect.disabled = !isUser;
        }

        if (planFeaturesSection) {
            planFeaturesSection.classList.toggle('d-none', !isUser);
            toggleSectionInputs(planFeaturesSection, isUser);
        }

        if (managerPermissionsSection) {
            managerPermissionsSection.classList.toggle('d-none', !isManager);
            toggleSectionInputs(managerPermissionsSection, isManager);
        }
    }

    roleSelect.addEventListener('change', syncRoleFields);
    syncRoleFields();
});
</script>
@endpush
