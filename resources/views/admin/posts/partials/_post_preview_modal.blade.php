{{-- Live 100% Authentic Platform Post Preview Modal --}}
<div class="modal fade" id="modal-post-preview" tabindex="-1" aria-labelledby="modalPostPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 680px;">
        <div class="modal-content border-0 shadow-xl" style="border-radius: 20px; overflow: hidden; background: #fafafa;">
            
            {{-- Modal Header: Platform Switcher & Device Toggle --}}
            <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-sm rounded-circle bg-dark-lt text-dark">
                        <i class="ti ti-eye fs-2"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalPostPreviewLabel" style="font-size: 1.1rem; letter-spacing: -0.2px;">Post Live Preview</h5>
                        <small class="text-secondary" style="font-size: 11.5px;">Live 100% authentic visual simulation</small>
                    </div>
                </div>

                {{-- Platform Switcher Tabs --}}
                <div class="d-flex align-items-center gap-1.5 p-1 bg-light rounded-pill border">
                    <button type="button" class="btn btn-xs rounded-pill px-3 py-1 fw-semibold preview-platform-tab active" data-platform="instagram" id="preview-tab-ig">
                        <i class="ti ti-brand-instagram text-pink me-1"></i>Instagram
                    </button>
                    <button type="button" class="btn btn-xs rounded-pill px-3 py-1 fw-semibold preview-platform-tab text-secondary" data-platform="facebook" id="preview-tab-fb">
                        <i class="ti ti-brand-facebook text-primary me-1"></i>Facebook
                    </button>
                    <button type="button" class="btn btn-xs rounded-pill px-3 py-1 fw-semibold preview-platform-tab text-secondary" data-platform="youtube" id="preview-tab-yt">
                        <i class="ti ti-brand-youtube text-danger me-1"></i>YouTube
                    </button>
                </div>

                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                    Close
                </button>
            </div>

            {{-- Modal Body: Canvas Area --}}
            <div class="modal-body p-3 p-md-4" style="background: #f1f5f9; min-height: 480px; max-height: 75vh; overflow-y: auto;">
                
                {{-- ========================================================================= -->
                {{-- 1. INSTAGRAM FEED POST PREVIEW (100% Authentic Instagram UI)               -->
                {{-- ========================================================================= -->
                <div id="preview-card-instagram" class="preview-platform-card mx-auto" style="max-width: 440px;">
                    <div class="bg-white border rounded-3 shadow-sm overflow-hidden" style="border-color: #dbdbdb !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                        
                        {{-- Instagram Header --}}
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom" style="border-color: #efefef !important;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="position-relative">
                                    <div class="rounded-circle p-0.5" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);">
                                        <img src="" class="rounded-circle bg-white p-0.5 preview-account-avatar" style="width: 34px; height: 34px; object-fit: cover;" alt="avatar">
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark preview-account-name" style="font-size: 13.5px; line-height: 1.2;">username</div>
                                    <div class="text-secondary" style="font-size: 11px;">Original audio</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-link p-0 text-dark text-decoration-none">
                                <i class="ti ti-dots fs-3"></i>
                            </button>
                        </div>

                        {{-- Instagram Media --}}
                        <div class="preview-media-container position-relative bg-dark d-flex align-items-center justify-content-center overflow-hidden" style="width: 100%; min-height: 380px; max-height: 480px;">
                            <img src="" class="preview-media-main img-fluid w-100 h-100" style="object-fit: contain; max-height: 480px;" alt="Post Media">
                            <video src="" class="preview-media-video w-100 h-100 d-none" style="object-fit: contain; max-height: 480px;" controls></video>
                            
                            {{-- Placeholder when no media uploaded --}}
                            <div class="preview-media-placeholder text-center text-white-50 p-4 d-none">
                                <i class="ti ti-photo fs-1 mb-2 d-block text-white-50"></i>
                                <span class="small">Upload a photo, video or Canva graphic to preview media</span>
                            </div>

                            {{-- Carousel indicators badge --}}
                            <div class="preview-carousel-badge position-absolute top-0 end-0 m-2.5 badge bg-dark bg-opacity-75 rounded-pill px-2.5 py-1 text-white d-none" style="font-size: 11px;">
                                <i class="ti ti-copy me-1"></i><span class="preview-carousel-count">1/1</span>
                            </div>
                        </div>

                        {{-- Instagram Action Bar --}}
                        <div class="p-3 pb-2">
                            <div class="d-flex align-items-center justify-content-between mb-2.5">
                                <div class="d-flex align-items-center gap-3 fs-2 text-dark">
                                    <i class="ti ti-heart cursor-pointer hover-opacity"></i>
                                    <i class="ti ti-message-circle cursor-pointer hover-opacity"></i>
                                    <i class="ti ti-send cursor-pointer hover-opacity"></i>
                                </div>
                                <div class="fs-2 text-dark">
                                    <i class="ti ti-bookmark cursor-pointer hover-opacity"></i>
                                </div>
                            </div>

                            {{-- Likes Count --}}
                            <div class="fw-bold text-dark mb-1.5" style="font-size: 13.5px;">
                                Liked by <span class="preview-account-name">socialmanager</span> and <span class="fw-bold">others</span>
                            </div>

                            {{-- Caption --}}
                            <div class="text-dark mb-2 preview-caption-box" style="font-size: 13.5px; line-height: 1.45; word-break: break-word;">
                                <span class="fw-bold me-1 preview-account-name">username</span>
                                <span class="preview-caption-text"></span>
                            </div>

                            {{-- First Comment Bubble (Auto-Drop) --}}
                            <div class="preview-first-comment-wrap p-2 rounded-2 mb-2 bg-light-subtle border d-none" style="font-size: 13px; line-height: 1.4;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-pink-lt text-pink rounded-pill px-2 py-0.5" style="font-size: 10px;">
                                        <i class="ti ti-message-circle me-1"></i>First Comment
                                    </span>
                                    <span class="text-muted" style="font-size: 10.5px;">Auto-dropped</span>
                                </div>
                                <div>
                                    <span class="fw-bold me-1 preview-account-name">username</span>
                                    <span class="preview-first-comment-text text-secondary"></span>
                                </div>
                            </div>

                            {{-- Comments Link & Timestamp --}}
                            <div class="text-secondary mb-1" style="font-size: 12.5px;">View all comments</div>
                            <div class="text-uppercase text-secondary" style="font-size: 10px; letter-spacing: 0.2px;">Just now</div>
                        </div>

                    </div>
                </div>

                {{-- ========================================================================= -->
                {{-- 2. FACEBOOK FEED POST PREVIEW (100% Authentic Facebook UI)                 -->
                {{-- ========================================================================= -->
                <div id="preview-card-facebook" class="preview-platform-card mx-auto d-none" style="max-width: 480px;">
                    <div class="bg-white border rounded-3 shadow-sm overflow-hidden" style="border-color: #ced0d4 !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                        
                        {{-- Facebook Header --}}
                        <div class="d-flex align-items-center justify-content-between p-3 pb-2">
                            <div class="d-flex align-items-center gap-2.5">
                                <img src="" class="rounded-circle preview-account-avatar" style="width: 40px; height: 40px; object-fit: cover;" alt="avatar">
                                <div>
                                    <div class="fw-bold text-dark preview-account-name" style="font-size: 14.5px; line-height: 1.2;">Page Name</div>
                                    <div class="text-secondary d-flex align-items-center gap-1" style="font-size: 12px;">
                                        <span>Just now</span>
                                        <span>&bull;</span>
                                        <i class="ti ti-world" style="font-size: 12px;" title="Public"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1 text-secondary">
                                <i class="ti ti-dots fs-3 cursor-pointer"></i>
                                <i class="ti ti-x fs-3 cursor-pointer ms-1"></i>
                            </div>
                        </div>

                        {{-- Facebook Caption / Text --}}
                        <div class="px-3 pb-2.5 text-dark preview-caption-box" style="font-size: 14.5px; line-height: 1.45; word-break: break-word;">
                            <span class="preview-caption-text"></span>
                        </div>

                        {{-- Facebook Media --}}
                        <div class="preview-media-container position-relative bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 100%; min-height: 280px; max-height: 440px; background: #000;">
                            <img src="" class="preview-media-main img-fluid w-100" style="object-fit: cover; max-height: 440px;" alt="Post Media">
                            <video src="" class="preview-media-video w-100 d-none" style="max-height: 440px;" controls></video>
                            
                            {{-- Placeholder when text-only or no media --}}
                            <div class="preview-media-placeholder text-center text-muted p-4 d-none">
                                <i class="ti ti-photo fs-1 mb-1 d-block text-secondary"></i>
                                <span class="small text-secondary">Text post &bull; No media attached</span>
                            </div>
                        </div>

                        {{-- Facebook Engagement Counters --}}
                        <div class="px-3 py-2 d-flex align-items-center justify-content-between text-secondary border-bottom" style="font-size: 13px; border-color: #e4e6eb !important;">
                            <div class="d-flex align-items-center gap-1">
                                <span class="d-inline-flex align-items-center justify-content-center text-white rounded-circle shadow-xs" style="width: 18px; height: 18px; background: #1877f2; font-size: 10px;">
                                    <i class="ti ti-thumb-up"></i>
                                </span>
                                <span class="d-inline-flex align-items-center justify-content-center text-white rounded-circle shadow-xs" style="width: 18px; height: 18px; background: #fa383e; font-size: 10px; margin-left: -5px;">
                                    <i class="ti ti-heart"></i>
                                </span>
                                <span class="ms-1">1</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span>1 comment</span>
                                <span>&bull;</span>
                                <span>0 shares</span>
                            </div>
                        </div>

                        {{-- Facebook Action Buttons Bar --}}
                        <div class="d-flex align-items-center justify-content-around py-1.5 px-2 border-bottom text-secondary fw-semibold" style="font-size: 13.5px; border-color: #e4e6eb !important;">
                            <div class="d-flex align-items-center gap-1.5 py-1 px-3 rounded-2 cursor-pointer hover-bg-light flex-grow-1 justify-content-center">
                                <i class="ti ti-thumb-up fs-2"></i>
                                <span>Like</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 py-1 px-3 rounded-2 cursor-pointer hover-bg-light flex-grow-1 justify-content-center">
                                <i class="ti ti-message-circle fs-2"></i>
                                <span>Comment</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 py-1 px-3 rounded-2 cursor-pointer hover-bg-light flex-grow-1 justify-content-center">
                                <i class="ti ti-share-3 fs-2"></i>
                                <span>Share</span>
                            </div>
                        </div>

                        {{-- Facebook First Comment Bubble --}}
                        <div class="p-3 pt-2.5 preview-first-comment-wrap d-none">
                            <div class="d-flex align-items-start gap-2">
                                <img src="" class="rounded-circle preview-account-avatar mt-0.5" style="width: 32px; height: 32px; object-fit: cover;" alt="avatar">
                                <div class="flex-grow-1">
                                    <div class="p-2.5 rounded-3" style="background: #f0f2f5; font-size: 13px; line-height: 1.4;">
                                        <div class="fw-bold text-dark preview-account-name">Page Name</div>
                                        <div class="preview-first-comment-text text-dark"></div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 ms-2 mt-1 text-secondary fw-semibold" style="font-size: 11px;">
                                        <span class="cursor-pointer">Like</span>
                                        <span class="cursor-pointer">Reply</span>
                                        <span class="fw-normal">Just now</span>
                                        <span class="badge bg-primary-lt text-primary px-1.5 py-0.5 rounded-pill" style="font-size: 9.5px;">First Comment</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ========================================================================= -->
                {{-- 3. YOUTUBE VIDEO WATCH PREVIEW (100% Authentic YouTube UI)                 -->
                {{-- ========================================================================= -->
                <div id="preview-card-youtube" class="preview-platform-card mx-auto d-none" style="max-width: 520px;">
                    <div class="bg-white border rounded-3 shadow-sm overflow-hidden" style="border-color: #e5e7eb !important; font-family: Roboto, -apple-system, BlinkMacSystemFont, sans-serif;">
                        
                        {{-- YouTube Player Box --}}
                        <div class="position-relative bg-black d-flex align-items-center justify-content-center overflow-hidden" style="width: 100%; aspect-ratio: 16/9;">
                            <img src="" class="preview-yt-thumb w-100 h-100" style="object-fit: cover;" alt="YouTube Thumbnail">
                            
                            {{-- YouTube Red Play Button --}}
                            <div class="position-absolute d-flex align-items-center justify-content-center rounded-3 shadow" style="width: 58px; height: 40px; background: rgba(255, 0, 0, 0.9); cursor: pointer;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                </svg>
                            </div>

                            {{-- Duration Badge --}}
                            <div class="position-absolute bottom-0 end-0 m-2 px-1.5 py-0.5 rounded text-white fw-bold bg-dark bg-opacity-75" style="font-size: 11px;">
                                12:45
                            </div>
                        </div>

                        {{-- YouTube Video Details --}}
                        <div class="p-3">
                            <h4 class="fw-bold text-dark mb-2" id="preview-yt-title-text" style="font-size: 16.5px; line-height: 1.35; letter-spacing: -0.2px;">
                                Video Title
                            </h4>

                            {{-- Channel Info & Subscribe Button --}}
                            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2.5">
                                    <img src="" class="rounded-circle preview-account-avatar" style="width: 36px; height: 36px; object-fit: cover;" alt="channel avatar">
                                    <div>
                                        <div class="fw-bold text-dark preview-account-name" style="font-size: 13.5px; line-height: 1.2;">Channel Name</div>
                                        <div class="text-secondary" style="font-size: 11px;">10.4K subscribers</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 py-1 fw-bold" style="font-size: 12px; background: #0f0f0f;">
                                        Subscribe
                                    </button>
                                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1.5 fw-semibold d-flex align-items-center gap-1" style="font-size: 12px;">
                                        <i class="ti ti-thumb-up"></i> 0 &bull; <i class="ti ti-thumb-down"></i>
                                    </span>
                                </div>
                            </div>

                            {{-- YouTube Description Box --}}
                            <div class="p-2.5 rounded-3 bg-light text-dark mb-3" style="font-size: 13px; line-height: 1.45;">
                                <div class="fw-bold text-dark mb-1" style="font-size: 12px;">0 views &bull; Just now</div>
                                <div id="preview-yt-desc-text" class="text-secondary" style="white-space: pre-line; word-break: break-word;">
                                    Video description preview will appear here...
                                </div>
                            </div>

                            {{-- YouTube Pinned First Comment --}}
                            <div class="preview-first-comment-wrap p-2.5 rounded-3 border bg-white d-none">
                                <div class="d-flex align-items-center gap-1.5 text-secondary small fw-semibold mb-1.5" style="font-size: 11px;">
                                    <i class="ti ti-pin text-dark"></i>
                                    <span>Pinned by <span class="preview-account-name">Channel</span></span>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <img src="" class="rounded-circle preview-account-avatar mt-0.5" style="width: 28px; height: 28px; object-fit: cover;" alt="avatar">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 12.5px;">
                                            <span class="preview-account-name">Channel</span>
                                            <span class="badge bg-secondary-lt text-secondary px-1 py-0 rounded" style="font-size: 9.5px;">Creator</span>
                                            <span class="text-muted fw-normal ms-1">Just now</span>
                                        </div>
                                        <div class="preview-first-comment-text text-dark mt-0.5" style="font-size: 13px; line-height: 1.4;"></div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-top py-2.5 px-4 bg-white d-flex align-items-center justify-content-between">
                <span class="text-secondary small">
                    <i class="ti ti-info-circle me-1"></i>Previews update in real-time as you type or change attachments.
                </span>
                <button type="button" class="btn btn-light rounded-pill px-4 py-1.5 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                    Close Preview
                </button>
            </div>

        </div>
    </div>
</div>

<style>
.preview-platform-tab.active {
    background: #ffffff !important;
    color: #0f172a !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
}
.hover-opacity:hover {
    opacity: 0.7;
    transition: opacity 0.15s ease;
}
.hover-bg-light:hover {
    background: #f2f2f2 !important;
    transition: background 0.15s ease;
}
</style>
