<?php

declare(strict_types=1);

namespace Modules\ProductStats\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class ProductStatsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge([
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'views' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'clicks' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'purchases' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'revenue' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'conversion_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'click_through_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'bounce_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'avg_session_duration' => [
                'nullable',
                'integer',
                'min:0',
                'max:86400',
            ],
            'avg_page_views' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'avg_order_value' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'lifetime_value' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'retention_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'churn_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'satisfaction_score' => [
                'nullable',
                'numeric',
                'min:0',
                'max:10',
            ],
            'rating' => [
                'nullable',
                'numeric',
                'min:0',
                'max:5',
            ],
            'review_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'wishlist_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'cart_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'share_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'like_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'comment_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'download_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'search_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'impression_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'engagement_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'reach' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'frequency' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'recency' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'monetary' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'rfm_score' => [
                'nullable',
                'integer',
                'min:0',
                'max:999',
            ],
            'segment' => [
                'nullable',
                'string',
                'max:100',
            ],
            'cohort' => [
                'nullable',
                'string',
                'max:100',
            ],
            'funnel_stage' => [
                'nullable',
                'string',
                'max:100',
            ],
            'attribution_source' => [
                'nullable',
                'string',
                'max:100',
            ],
            'attribution_medium' => [
                'nullable',
                'string',
                'max:100',
            ],
            'attribution_campaign' => [
                'nullable',
                'string',
                'max:100',
            ],
            'attribution_term' => [
                'nullable',
                'string',
                'max:100',
            ],
            'attribution_content' => [
                'nullable',
                'string',
                'max:100',
            ],
            'device_type' => [
                'nullable',
                'string',
                'in:mobile,tablet,desktop,other',
            ],
            'browser' => [
                'nullable',
                'string',
                'max:100',
            ],
            'os' => [
                'nullable',
                'string',
                'max:100',
            ],
            'country' => [
                'nullable',
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/',
            ],
            'city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'language' => [
                'nullable',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'currency' => [
                'nullable',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],
            'referrer' => [
                'nullable',
                'url',
                'max:500',
            ],
            'utm_source' => [
                'nullable',
                'string',
                'max:100',
            ],
            'utm_medium' => [
                'nullable',
                'string',
                'max:100',
            ],
            'utm_campaign' => [
                'nullable',
                'string',
                'max:100',
            ],
            'utm_term' => [
                'nullable',
                'string',
                'max:100',
            ],
            'utm_content' => [
                'nullable',
                'string',
                'max:100',
            ],
            'start_date' => [
                'nullable',
                'date',
                'before_or_equal:end_date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'period' => [
                'nullable',
                'string',
                'in:daily,weekly,monthly,quarterly,yearly',
            ],
            'granularity' => [
                'nullable',
                'string',
                'in:hour,day,week,month,quarter,year',
            ],
            'timezone' => [
                'nullable',
                'string',
                'max:50',
            ],
            'locale' => [
                'nullable',
                'string',
                'max:10',
            ],
            'format' => [
                'nullable',
                'string',
                'in:json,xml,csv,excel,pdf,html,text',
            ],
            'include_metadata' => [
                'boolean',
            ],
            'include_breakdown' => [
                'boolean',
            ],
            'include_comparison' => [
                'boolean',
            ],
            'include_trends' => [
                'boolean',
            ],
            'include_forecasts' => [
                'boolean',
            ],
            'include_recommendations' => [
                'boolean',
            ],
            'include_alerts' => [
                'boolean',
            ],
            'include_warnings' => [
                'boolean',
            ],
            'include_errors' => [
                'boolean',
            ],
            'include_debug' => [
                'boolean',
            ],
            'include_trace' => [
                'boolean',
            ],
            'include_logs' => [
                'boolean',
            ],
            'include_metrics' => [
                'boolean',
            ],
            'include_kpis' => [
                'boolean',
            ],
            'include_benchmarks' => [
                'boolean',
            ],
            'include_targets' => [
                'boolean',
            ],
            'include_goals' => [
                'boolean',
            ],
            'include_objectives' => [
                'boolean',
            ],
            'include_strategies' => [
                'boolean',
            ],
            'include_tactics' => [
                'boolean',
            ],
            'include_actions' => [
                'boolean',
            ],
            'include_tasks' => [
                'boolean',
            ],
            'include_activities' => [
                'boolean',
            ],
            'include_events' => [
                'boolean',
            ],
            'include_incidents' => [
                'boolean',
            ],
            'include_issues' => [
                'boolean',
            ],
            'include_problems' => [
                'boolean',
            ],
            'include_solutions' => [
                'boolean',
            ],
            'include_improvements' => [
                'boolean',
            ],
            'include_optimizations' => [
                'boolean',
            ],
            'include_enhancements' => [
                'boolean',
            ],
            'include_upgrades' => [
                'boolean',
            ],
            'include_downgrades' => [
                'boolean',
            ],
            'include_migrations' => [
                'boolean',
            ],
            'include_transitions' => [
                'boolean',
            ],
            'include_changes' => [
                'boolean',
            ],
            'include_updates' => [
                'boolean',
            ],
            'include_patches' => [
                'boolean',
            ],
            'include_fixes' => [
                'boolean',
            ],
            'include_bugs' => [
                'boolean',
            ],
            'include_features' => [
                'boolean',
            ],
            'include_requirements' => [
                'boolean',
            ],
            'include_specifications' => [
                'boolean',
            ],
            'include_documentation' => [
                'boolean',
            ],
            'include_guides' => [
                'boolean',
            ],
            'include_tutorials' => [
                'boolean',
            ],
            'include_manuals' => [
                'boolean',
            ],
            'include_help' => [
                'boolean',
            ],
            'include_support' => [
                'boolean',
            ],
            'include_faq' => [
                'boolean',
            ],
            'include_contact' => [
                'boolean',
            ],
            'include_about' => [
                'boolean',
            ],
            'include_privacy' => [
                'boolean',
            ],
            'include_terms' => [
                'boolean',
            ],
            'include_legal' => [
                'boolean',
            ],
            'include_compliance' => [
                'boolean',
            ],
            'include_security' => [
                'boolean',
            ],
            'include_privacy_policy' => [
                'boolean',
            ],
            'include_terms_of_service' => [
                'boolean',
            ],
            'include_terms_of_use' => [
                'boolean',
            ],
            'include_terms_and_conditions' => [
                'boolean',
            ],
            'include_user_agreement' => [
                'boolean',
            ],
            'include_license_agreement' => [
                'boolean',
            ],
            'include_service_agreement' => [
                'boolean',
            ],
            'include_master_agreement' => [
                'boolean',
            ],
            'include_contract' => [
                'boolean',
            ],
            'include_agreement' => [
                'boolean',
            ],
            'include_policy' => [
                'boolean',
            ],
            'include_procedure' => [
                'boolean',
            ],
            'include_process' => [
                'boolean',
            ],
            'include_workflow' => [
                'boolean',
            ],
            'include_integration' => [
                'boolean',
            ],
            'include_api' => [
                'boolean',
            ],
            'include_webhook' => [
                'boolean',
            ],
            'include_sdk' => [
                'boolean',
            ],
            'include_library' => [
                'boolean',
            ],
            'include_framework' => [
                'boolean',
            ],
            'include_platform' => [
                'boolean',
            ],
            'include_ecosystem' => [
                'boolean',
            ],
            'include_environment' => [
                'boolean',
            ],
            'include_infrastructure' => [
                'boolean',
            ],
            'include_architecture' => [
                'boolean',
            ],
            'include_design' => [
                'boolean',
            ],
            'include_implementation' => [
                'boolean',
            ],
            'include_deployment' => [
                'boolean',
            ],
            'include_configuration' => [
                'boolean',
            ],
            'include_setup' => [
                'boolean',
            ],
            'include_installation' => [
                'boolean',
            ],
            'include_maintenance' => [
                'boolean',
            ],
            'include_monitoring' => [
                'boolean',
            ],
            'include_logging' => [
                'boolean',
            ],
            'include_auditing' => [
                'boolean',
            ],
            'include_reporting' => [
                'boolean',
            ],
            'include_analytics' => [
                'boolean',
            ],
            'include_insights' => [
                'boolean',
            ],
            'include_intelligence' => [
                'boolean',
            ],
            'include_ai' => [
                'boolean',
            ],
            'include_ml' => [
                'boolean',
            ],
            'include_dl' => [
                'boolean',
            ],
            'include_nlp' => [
                'boolean',
            ],
            'include_cv' => [
                'boolean',
            ],
            'include_robotics' => [
                'boolean',
            ],
            'include_automation' => [
                'boolean',
            ],
            'include_iot' => [
                'boolean',
            ],
            'include_blockchain' => [
                'boolean',
            ],
            'include_crypto' => [
                'boolean',
            ],
            'include_nft' => [
                'boolean',
            ],
            'include_metaverse' => [
                'boolean',
            ],
            'include_ar' => [
                'boolean',
            ],
            'include_vr' => [
                'boolean',
            ],
            'include_mr' => [
                'boolean',
            ],
            'include_xr' => [
                'boolean',
            ],
            'include_quantum' => [
                'boolean',
            ],
            'include_edge' => [
                'boolean',
            ],
            'include_cloud' => [
                'boolean',
            ],
            'include_hybrid' => [
                'boolean',
            ],
            'include_multi' => [
                'boolean',
            ],
            'include_cross' => [
                'boolean',
            ],
            'include_omni' => [
                'boolean',
            ],
            'include_uni' => [
                'boolean',
            ],
            'include_bi' => [
                'boolean',
            ],
            'include_tri' => [
                'boolean',
            ],
            'include_quad' => [
                'boolean',
            ],
            'include_penta' => [
                'boolean',
            ],
            'include_hexa' => [
                'boolean',
            ],
            'include_hepta' => [
                'boolean',
            ],
            'include_octa' => [
                'boolean',
            ],
            'include_nona' => [
                'boolean',
            ],
            'include_deca' => [
                'boolean',
            ],
            'include_centi' => [
                'boolean',
            ],
            'include_milli' => [
                'boolean',
            ],
            'include_micro' => [
                'boolean',
            ],
            'include_nano' => [
                'boolean',
            ],
            'include_pico' => [
                'boolean',
            ],
            'include_femto' => [
                'boolean',
            ],
            'include_atto' => [
                'boolean',
            ],
            'include_zepto' => [
                'boolean',
            ],
            'include_yocto' => [
                'boolean',
            ],
            'include_kilo' => [
                'boolean',
            ],
            'include_mega' => [
                'boolean',
            ],
            'include_giga' => [
                'boolean',
            ],
            'include_tera' => [
                'boolean',
            ],
            'include_peta' => [
                'boolean',
            ],
            'include_exa' => [
                'boolean',
            ],
            'include_zetta' => [
                'boolean',
            ],
            'include_yotta' => [
                'boolean',
            ],
            'include_other' => [
                'boolean',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'product_id.required' => 'Product is required.',
            'product_id.exists' => 'Product does not exist.',
            'views.min' => 'Views must be at least 0.',
            'views.max' => 'Views cannot exceed 999999.',
            'clicks.min' => 'Clicks must be at least 0.',
            'clicks.max' => 'Clicks cannot exceed 999999.',
            'purchases.min' => 'Purchases must be at least 0.',
            'purchases.max' => 'Purchases cannot exceed 999999.',
            'revenue.min' => 'Revenue must be at least 0.',
            'revenue.max' => 'Revenue cannot exceed 999999.99.',
            'conversion_rate.min' => 'Conversion rate must be at least 0.',
            'conversion_rate.max' => 'Conversion rate cannot exceed 100.',
            'click_through_rate.min' => 'Click through rate must be at least 0.',
            'click_through_rate.max' => 'Click through rate cannot exceed 100.',
            'bounce_rate.min' => 'Bounce rate must be at least 0.',
            'bounce_rate.max' => 'Bounce rate cannot exceed 100.',
            'avg_session_duration.min' => 'Average session duration must be at least 0.',
            'avg_session_duration.max' => 'Average session duration cannot exceed 86400 seconds.',
            'avg_page_views.min' => 'Average page views must be at least 0.',
            'avg_page_views.max' => 'Average page views cannot exceed 999999.99.',
            'avg_order_value.min' => 'Average order value must be at least 0.',
            'avg_order_value.max' => 'Average order value cannot exceed 999999.99.',
            'lifetime_value.min' => 'Lifetime value must be at least 0.',
            'lifetime_value.max' => 'Lifetime value cannot exceed 999999.99.',
            'retention_rate.min' => 'Retention rate must be at least 0.',
            'retention_rate.max' => 'Retention rate cannot exceed 100.',
            'churn_rate.min' => 'Churn rate must be at least 0.',
            'churn_rate.max' => 'Churn rate cannot exceed 100.',
            'satisfaction_score.min' => 'Satisfaction score must be at least 0.',
            'satisfaction_score.max' => 'Satisfaction score cannot exceed 10.',
            'rating.min' => 'Rating must be at least 0.',
            'rating.max' => 'Rating cannot exceed 5.',
            'review_count.min' => 'Review count must be at least 0.',
            'review_count.max' => 'Review count cannot exceed 999999.',
            'wishlist_count.min' => 'Wishlist count must be at least 0.',
            'wishlist_count.max' => 'Wishlist count cannot exceed 999999.',
            'cart_count.min' => 'Cart count must be at least 0.',
            'cart_count.max' => 'Cart count cannot exceed 999999.',
            'share_count.min' => 'Share count must be at least 0.',
            'share_count.max' => 'Share count cannot exceed 999999.',
            'like_count.min' => 'Like count must be at least 0.',
            'like_count.max' => 'Like count cannot exceed 999999.',
            'comment_count.min' => 'Comment count must be at least 0.',
            'comment_count.max' => 'Comment count cannot exceed 999999.',
            'download_count.min' => 'Download count must be at least 0.',
            'download_count.max' => 'Download count cannot exceed 999999.',
            'search_count.min' => 'Search count must be at least 0.',
            'search_count.max' => 'Search count cannot exceed 999999.',
            'impression_count.min' => 'Impression count must be at least 0.',
            'impression_count.max' => 'Impression count cannot exceed 999999.',
            'engagement_rate.min' => 'Engagement rate must be at least 0.',
            'engagement_rate.max' => 'Engagement rate cannot exceed 100.',
            'reach.min' => 'Reach must be at least 0.',
            'reach.max' => 'Reach cannot exceed 999999.',
            'frequency.min' => 'Frequency must be at least 0.',
            'frequency.max' => 'Frequency cannot exceed 999999.99.',
            'recency.min' => 'Recency must be at least 0.',
            'recency.max' => 'Recency cannot exceed 999999.',
            'monetary.min' => 'Monetary must be at least 0.',
            'monetary.max' => 'Monetary cannot exceed 999999.99.',
            'rfm_score.min' => 'RFM score must be at least 0.',
            'rfm_score.max' => 'RFM score cannot exceed 999.',
            'segment.max' => 'Segment must not exceed 100 characters.',
            'cohort.max' => 'Cohort must not exceed 100 characters.',
            'funnel_stage.max' => 'Funnel stage must not exceed 100 characters.',
            'attribution_source.max' => 'Attribution source must not exceed 100 characters.',
            'attribution_medium.max' => 'Attribution medium must not exceed 100 characters.',
            'attribution_campaign.max' => 'Attribution campaign must not exceed 100 characters.',
            'attribution_term.max' => 'Attribution term must not exceed 100 characters.',
            'attribution_content.max' => 'Attribution content must not exceed 100 characters.',
            'device_type.in' => 'Device type must be mobile, tablet, desktop, or other.',
            'browser.max' => 'Browser must not exceed 100 characters.',
            'os.max' => 'OS must not exceed 100 characters.',
            'country.size' => 'Country must be exactly 2 characters long.',
            'country.regex' => 'Country must be in uppercase format (e.g., US, CA, GB).',
            'city.max' => 'City must not exceed 100 characters.',
            'language.size' => 'Language must be exactly 2 characters long.',
            'language.regex' => 'Language must be in lowercase format (e.g., en, es, fr).',
            'currency.size' => 'Currency must be exactly 3 characters long.',
            'currency.regex' => 'Currency must be in uppercase format (e.g., USD, EUR, GBP).',
            'referrer.url' => 'Please enter a valid referrer URL.',
            'referrer.max' => 'Referrer must not exceed 500 characters.',
            'utm_source.max' => 'UTM source must not exceed 100 characters.',
            'utm_medium.max' => 'UTM medium must not exceed 100 characters.',
            'utm_campaign.max' => 'UTM campaign must not exceed 100 characters.',
            'utm_term.max' => 'UTM term must not exceed 100 characters.',
            'utm_content.max' => 'UTM content must not exceed 100 characters.',
            'start_date.before_or_equal' => 'Start date must be before or equal to end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'period.in' => 'Period must be daily, weekly, monthly, quarterly, or yearly.',
            'granularity.in' => 'Granularity must be hour, day, week, month, quarter, or year.',
            'timezone.max' => 'Timezone must not exceed 50 characters.',
            'locale.max' => 'Locale must not exceed 10 characters.',
            'format.in' => 'Format must be json, xml, csv, excel, pdf, html, or text.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate product
            if ($this->filled('product_id')) {
                $productId = $this->product_id;
                $product = \Modules\Product\Models\Product::find($productId);

                if (! $product) {
                    $validator->errors()->add(
                        'product_id',
                        'Product does not exist.'
                    );
                } elseif (! $product->is_active) {
                    $validator->errors()->add(
                        'product_id',
                        'Product is not active.'
                    );
                }
            }

            // Validate date ranges
            if ($this->filled('start_date') && $this->filled('end_date')) {
                $startDate = $this->start_date;
                $endDate = $this->end_date;

                if ($startDate >= $endDate) {
                    $validator->errors()->add(
                        'end_date',
                        'End date must be after start date.'
                    );
                }
            }

            // Validate period
            if ($this->filled('period')) {
                $period = $this->period;
                $validPeriods = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'];

                if (! in_array($period, $validPeriods)) {
                    $validator->errors()->add(
                        'period',
                        'Invalid period selected.'
                    );
                }
            }

            // Validate granularity
            if ($this->filled('granularity')) {
                $granularity = $this->granularity;
                $validGranularities = ['hour', 'day', 'week', 'month', 'quarter', 'year'];

                if (! in_array($granularity, $validGranularities)) {
                    $validator->errors()->add(
                        'granularity',
                        'Invalid granularity selected.'
                    );
                }
            }

            // Validate format
            if ($this->filled('format')) {
                $format = $this->format;
                $validFormats = ['json', 'xml', 'csv', 'excel', 'pdf', 'html', 'text'];

                if (! in_array($format, $validFormats)) {
                    $validator->errors()->add(
                        'format',
                        'Invalid format selected.'
                    );
                }
            }

            // Validate device type
            if ($this->filled('device_type')) {
                $deviceType = $this->device_type;
                $validTypes = ['mobile', 'tablet', 'desktop', 'other'];

                if (! in_array($deviceType, $validTypes)) {
                    $validator->errors()->add(
                        'device_type',
                        'Invalid device type.'
                    );
                }
            }

            // Validate country
            if ($this->filled('country')) {
                $country = $this->country;

                if (mb_strlen($country) !== 2) {
                    $validator->errors()->add(
                        'country',
                        'Country must be exactly 2 characters long.'
                    );
                }

                if (! preg_match('/^[A-Z]{2}$/', $country)) {
                    $validator->errors()->add(
                        'country',
                        'Country must be in uppercase format (e.g., US, CA, GB).'
                    );
                }
            }

            // Validate language
            if ($this->filled('language')) {
                $language = $this->language;

                if (mb_strlen($language) !== 2) {
                    $validator->errors()->add(
                        'language',
                        'Language must be exactly 2 characters long.'
                    );
                }

                if (! preg_match('/^[a-z]{2}$/', $language)) {
                    $validator->errors()->add(
                        'language',
                        'Language must be in lowercase format (e.g., en, es, fr).'
                    );
                }
            }

            // Validate currency
            if ($this->filled('currency')) {
                $currency = $this->currency;

                if (mb_strlen($currency) !== 3) {
                    $validator->errors()->add(
                        'currency',
                        'Currency must be exactly 3 characters long.'
                    );
                }

                if (! preg_match('/^[A-Z]{3}$/', $currency)) {
                    $validator->errors()->add(
                        'currency',
                        'Currency must be in uppercase format (e.g., USD, EUR, GBP).'
                    );
                }
            }

            // Validate referrer
            if ($this->filled('referrer')) {
                $referrer = $this->referrer;

                if (! filter_var($referrer, FILTER_VALIDATE_URL)) {
                    $validator->errors()->add(
                        'referrer',
                        'Please enter a valid referrer URL.'
                    );
                }
            }

            // Validate timezone
            if ($this->filled('timezone')) {
                $timezone = $this->timezone;

                if (! in_array($timezone, timezone_identifiers_list())) {
                    $validator->errors()->add(
                        'timezone',
                        'Invalid timezone.'
                    );
                }
            }

            // Validate locale
            if ($this->filled('locale') && mb_strlen($this->locale) > 10) {
                $validator->errors()->add(
                    'locale',
                    'Locale must not exceed 10 characters.'
                );
            }
        });
    }
}
