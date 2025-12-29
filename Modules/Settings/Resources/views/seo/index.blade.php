@extends('admin::layouts.master')

@section('title', 'SEO Settings')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">SEO Settings</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('settings.seo.update', $settings) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h4>Meta Tags</h4>
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input type="text" class="form-control" id="meta_title" 
                                       name="meta_title" 
                                       value="{{ $seoSettings['meta_title'] ?? '' }}"
                                       maxlength="255">
                                <small class="form-text text-muted">Recommended: 50-60 characters</small>
                            </div>
                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea class="form-control" id="meta_description" 
                                          name="meta_description" rows="3"
                                          maxlength="500">{{ $seoSettings['meta_description'] ?? '' }}</textarea>
                                <small class="form-text text-muted">Recommended: 150-160 characters</small>
                            </div>
                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input type="text" class="form-control" id="meta_keywords" 
                                       name="meta_keywords" 
                                       value="{{ $seoSettings['meta_keywords'] ?? '' }}"
                                       maxlength="500">
                                <small class="form-text text-muted">Comma-separated keywords</small>
                            </div>

                            <h4 class="mt-4">Open Graph (OG) Tags</h4>
                            <div class="form-group">
                                <label for="og_title">OG Title</label>
                                <input type="text" class="form-control" id="og_title" 
                                       name="og_title" 
                                       value="{{ $seoSettings['og_title'] ?? '' }}"
                                       maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="og_description">OG Description</label>
                                <textarea class="form-control" id="og_description" 
                                          name="og_description" rows="3"
                                          maxlength="500">{{ $seoSettings['og_description'] ?? '' }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="og_image">OG Image URL</label>
                                <input type="text" class="form-control" id="og_image" 
                                       name="og_image" 
                                       value="{{ $seoSettings['og_image'] ?? '' }}"
                                       maxlength="255">
                            </div>

                            <h4 class="mt-4">Twitter Card</h4>
                            <div class="form-group">
                                <label for="twitter_card">Twitter Card Type</label>
                                <select class="form-control" id="twitter_card" name="twitter_card">
                                    <option value="summary" {{ ($seoSettings['twitter_card'] ?? 'summary') == 'summary' ? 'selected' : '' }}>Summary</option>
                                    <option value="summary_large_image" {{ ($seoSettings['twitter_card'] ?? '') == 'summary_large_image' ? 'selected' : '' }}>Summary Large Image</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="twitter_site">Twitter Site</label>
                                <input type="text" class="form-control" id="twitter_site" 
                                       name="twitter_site" 
                                       value="{{ $seoSettings['twitter_site'] ?? '' }}"
                                       maxlength="255">
                                <small class="form-text text-muted">e.g., @yourusername</small>
                            </div>

                            <h4 class="mt-4">Analytics & Tracking</h4>
                            <div class="form-group">
                                <label for="google_analytics_id">Google Analytics ID</label>
                                <input type="text" class="form-control" id="google_analytics_id" 
                                       name="google_analytics_id" 
                                       value="{{ $seoSettings['google_analytics_id'] ?? '' }}"
                                       maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="google_tag_manager_id">Google Tag Manager ID</label>
                                <input type="text" class="form-control" id="google_tag_manager_id" 
                                       name="google_tag_manager_id" 
                                       value="{{ $seoSettings['google_tag_manager_id'] ?? '' }}"
                                       maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="facebook_pixel_id">Facebook Pixel ID</label>
                                <input type="text" class="form-control" id="facebook_pixel_id" 
                                       name="facebook_pixel_id" 
                                       value="{{ $seoSettings['facebook_pixel_id'] ?? '' }}"
                                       maxlength="255">
                            </div>

                            <h4 class="mt-4">Other Settings</h4>
                            <div class="form-group">
                                <label for="robots_txt">Robots.txt Content</label>
                                <textarea class="form-control" id="robots_txt" 
                                          name="robots_txt" rows="5">{{ $seoSettings['robots_txt'] ?? '' }}</textarea>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="sitemap_enabled" 
                                           name="sitemap_enabled" value="1" 
                                           {{ ($seoSettings['sitemap_enabled'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sitemap_enabled">
                                        Enable Sitemap
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Save SEO Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

