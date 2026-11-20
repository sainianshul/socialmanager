<script>
(function ($) {
    'use strict';

    let currentVariations = [];
    let currentHashtags = [];
    let activeVariationIndex = 0;

    // Modal show hook: pre-fill topic if user already started writing caption
    $('#modal-ai-caption').on('show.bs.modal', function () {
        let existingContent = $.trim($('#composer-content').val());
        let currentTopic = $.trim($('#ai-studio-topic').val());
        if (!currentTopic && existingContent) {
            let firstLine = existingContent.split('\n')[0].substring(0, 150);
            $('#ai-studio-topic').val(firstLine);
        }

        // Sync platform dropdown with active platform if available
        let activePlatform = $('#selected-platform').val();
        if (activePlatform == 2) {
            $('#ai-studio-platform').val('instagram');
        } else if (activePlatform == 1) {
            $('#ai-studio-platform').val('facebook');
        }

        setTimeout(function() {
            $('#ai-studio-topic').focus();
        }, 300);
    });

    // Quick Idea Preset Pills
    $(document).on('click', '.ai-preset-btn', function () {
        let presetText = $(this).data('preset');
        $('#ai-studio-topic').val(presetText).focus();
        $('.ai-preset-btn').removeClass('bg-primary text-white').addClass('bg-light text-secondary');
        $(this).removeClass('bg-light text-secondary').addClass('bg-primary text-white');
    });

    // Execute Generation
    $('#btn-run-ai-caption, #btn-rerun-ai-studio').on('click', function (e) {
        e.preventDefault();

        let topic = $.trim($('#ai-studio-topic').val());
        if (!topic || topic.length < 2) {
            if (typeof showToast === 'function') {
                showToast('Please enter what this post is about.', 'warning');
            } else {
                alert('Please enter what this post is about.');
            }
            $('#ai-studio-topic').focus();
            return;
        }

        let tone = $('#ai-studio-tone').val() || 'engaging';
        let platform = $('#ai-studio-platform').val() || 'all';
        let language = $('#ai-studio-language').val() || 'English';

        let $btn = $('#btn-run-ai-caption');
        let $btnText = $('#btn-run-ai-text');

        $btn.prop('disabled', true);
        $btnText.html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Generating viral hooks & hashtags...');

        $.ajax({
            url: "{{ route('admin.posts.generate-ai-caption') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                topic: topic,
                tone: tone,
                platform: platform,
                language: language
            },
            success: function (res) {
                if (res.success) {
                    renderAiResults(res);
                } else {
                    let errMsg = res.message || 'Could not generate caption. Please try again.';
                    if (typeof showToast === 'function') {
                        showToast(errMsg, 'error');
                    } else {
                        alert(errMsg);
                    }
                }
            },
            error: function (xhr) {
                let msg = 'Failed to generate AI caption. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                if (typeof showToast === 'function') {
                    showToast(msg, 'error');
                } else {
                    alert(msg);
                }
            },
            complete: function () {
                $btn.prop('disabled', false);
                $btnText.html('Generate Caption');
            }
        });
    });

    // Render Results
    function renderAiResults(data) {
        currentVariations = data.variations || [];
        if (!currentVariations.length && data.caption) {
            currentVariations = [
                { title: 'Primary Hook & Story', text: data.caption }
            ];
        }

        currentHashtags = (data.hashtags || []).map(function (tag) {
            let t = String(tag).trim();
            return t.startsWith('#') ? t : '#' + t;
        });

        activeVariationIndex = 0;
        updateVariationTabs();
        updateCaptionPreview();
        renderHashtagsCloud();

        $('#ai-studio-results').removeClass('d-none');
    }

    // Update Variation Tabs
    function updateVariationTabs() {
        let $wrap = $('#ai-variation-pills');
        $wrap.find('.ai-var-btn').remove();

        currentVariations.forEach(function (v, idx) {
            let activeClass = idx === activeVariationIndex ? 'active btn-primary text-white' : 'btn-outline-primary';
            let btnHtml = '<button type="button" class="btn btn-xs rounded-pill ' + activeClass + ' flex-shrink-0 ai-var-btn px-2.5 py-1" data-index="' + idx + '">' + ('Option ' + (idx + 1)) + '</button>';
            $wrap.append(btnHtml);
        });
    }

    // Switch Variation
    $(document).on('click', '.ai-var-btn', function () {
        activeVariationIndex = parseInt($(this).data('index'), 10) || 0;
        $('.ai-var-btn').removeClass('active btn-primary text-white').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('active btn-primary text-white');
        updateCaptionPreview();
    });

    // Update Caption Preview
    function updateCaptionPreview() {
        let currentText = '';
        if (currentVariations[activeVariationIndex]) {
            currentText = currentVariations[activeVariationIndex].text;
        }
        $('#ai-caption-preview').val(currentText);
    }

    // Render Hashtags Cloud
    function renderHashtagsCloud() {
        let $cloud = $('#ai-hashtags-cloud');
        $cloud.empty();

        if (!currentHashtags.length) {
            $cloud.append('<span class="text-secondary small">No hashtags generated.</span>');
            return;
        }

        currentHashtags.forEach(function (tag) {
            let pill = $('<span class="badge bg-primary-lt text-primary cursor-pointer user-select-none ai-hashtag-chip active py-1.5 px-2.5" style="transition: all 0.15s ease;">' + tag + '</span>');
            pill.data('tag', tag);
            $cloud.append(pill);
        });
    }

    // Toggle single hashtag active / inactive
    $(document).on('click', '.ai-hashtag-chip', function () {
        let $chip = $(this);
        $chip.toggleClass('active bg-primary-lt text-primary bg-light text-secondary');
    });

    // Select / Deselect All Hashtags
    $('#btn-toggle-all-hashtags').on('click', function () {
        let $chips = $('.ai-hashtag-chip');
        let anyInactive = $chips.filter(':not(.active)').length > 0;

        if (anyInactive) {
            $chips.addClass('active bg-primary-lt text-primary').removeClass('bg-light text-secondary');
            $(this).text('Deselect All');
        } else {
            $chips.removeClass('active bg-primary-lt text-primary').addClass('bg-light text-secondary');
            $(this).text('Select All');
        }
    });

    // Helper: Collect Active Hashtags
    function getActiveHashtags() {
        let tags = [];
        $('.ai-hashtag-chip.active').each(function () {
            tags.push($(this).data('tag') || $(this).text().trim());
        });
        return tags;
    }

    // Helper: Build Full Text
    function buildFullText() {
        let body = $.trim($('#ai-caption-preview').val());
        let tags = getActiveHashtags();
        if (tags.length > 0) {
            return body + "\n\n" + tags.join(' ');
        }
        return body;
    }

    // Apply to Post Textarea
    $('#btn-apply-ai-caption').on('click', function () {
        let fullText = buildFullText();
        if (!fullText) {
            if (typeof showToast === 'function') {
                showToast('Nothing to insert.', 'warning');
            } else {
                alert('Nothing to insert.');
            }
            return;
        }

        // Insert into Meta content textarea
        $('#composer-content').val(fullText).trigger('input');

        // If in YouTube mode, also insert into description and populate tags
        if ($('#youtube-description').length > 0 && !$('#youtube-fields-section').hasClass('d-none')) {
            $('#youtube-description').val(fullText).trigger('input');
            let tags = getActiveHashtags().map(t => t.replace('#', ''));
            if (tags.length > 0) {
                let existingTags = $.trim($('#youtube-tags').val());
                let combinedTags = existingTags ? existingTags + ', ' + tags.join(', ') : tags.join(', ');
                $('#youtube-tags').val(combinedTags);
            }
        }

        // Close Modal
        let modalEl = document.getElementById('modal-ai-caption');
        if (modalEl) {
            let modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
        }

        if (typeof showToast === 'function') {
            showToast('Caption applied to post successfully!', 'success');
        }
    });

    // Copy Full Text
    $('#btn-copy-full-ai-text').on('click', function () {
        let fullText = buildFullText();
        if (!fullText) return;

        if (navigator.clipboard) {
            navigator.clipboard.writeText(fullText).then(function () {
                if (typeof showToast === 'function') {
                    showToast('Full caption & hashtags copied to clipboard!', 'success');
                } else {
                    alert('Copied to clipboard!');
                }
            });
        }
    });

    // Copy Only Hashtags
    $('#btn-copy-only-hashtags').on('click', function () {
        let tags = getActiveHashtags();
        if (!tags.length) return;

        let tagText = tags.join(' ');
        if (navigator.clipboard) {
            navigator.clipboard.writeText(tagText).then(function () {
                if (typeof showToast === 'function') {
                    showToast('Hashtags copied to clipboard!', 'success');
                } else {
                    alert('Hashtags copied!');
                }
            });
        }
    });
})(jQuery);
</script>
