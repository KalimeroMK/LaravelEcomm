/**
 * Analytics Tracking Script
 * Tracks user behavior and sends data to the backend
 */

class AnalyticsTracker {
    constructor() {
        this.sessionId = this.generateSessionId();
        this.userId = this.getUserId();
        this.startTime = Date.now();
        this.pageStartTime = Date.now();
        this.events = [];
        this.isTracking = true;
        
        this.init();
    }

    /**
     * Initialize tracking
     */
    init() {
        this.trackPageView();
        this.setupEventListeners();
        this.setupPageUnload();
        this.setupVisibilityChange();
    }

    /**
     * Generate unique session ID
     */
    generateSessionId() {
        let sessionId = localStorage.getItem('analytics_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('analytics_session_id', sessionId);
        }
        return sessionId;
    }

    /**
     * Get user ID from authentication
     */
    getUserId() {
        // This would be set by your authentication system
        return window.userId || null;
    }

    /**
     * Track page view
     */
    trackPageView() {
        const eventData = {
            event_type: 'page_view',
            page_url: window.location.href,
            page_title: document.title,
            referrer: document.referrer,
            user_agent: navigator.userAgent,
            event_timestamp: new Date().toISOString(),
            event_data: {
                viewport_width: window.innerWidth,
                viewport_height: window.innerHeight,
                screen_width: screen.width,
                screen_height: screen.height,
                language: navigator.language,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            }
        };

        this.sendEvent(eventData);
    }

    /**
     * Track click events
     */
    trackClick(element, event) {
        const eventData = {
            event_type: 'click',
            page_url: window.location.href,
            page_title: document.title,
            event_timestamp: new Date().toISOString(),
            event_data: {
                element_tag: element.tagName,
                element_id: element.id,
                element_class: element.className,
                element_text: element.textContent?.substring(0, 100),
                element_href: element.href,
                click_x: event.clientX,
                click_y: event.clientY,
                target_element: element.getAttribute('data-track') || 'unknown',
            }
        };

        this.sendEvent(eventData);
    }

    /**
     * Track scroll events
     */
    trackScroll() {
        const scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
        
        const eventData = {
            event_type: 'scroll',
            page_url: window.location.href,
            page_title: document.title,
            event_timestamp: new Date().toISOString(),
            event_data: {
                scroll_percent: scrollPercent,
                scroll_y: window.scrollY,
                document_height: document.body.scrollHeight,
                viewport_height: window.innerHeight,
            }
        };

        this.sendEvent(eventData);
    }

    /**
     * Track time on page
     */
    trackTimeOnPage() {
        const timeOnPage = Math.round((Date.now() - this.pageStartTime) / 1000);
        
        const eventData = {
            event_type: 'time_on_page',
            page_url: window.location.href,
            page_title: document.title,
            duration: timeOnPage,
            event_timestamp: new Date().toISOString(),
            event_data: {
                time_on_page: timeOnPage,
                page_load_time: this.getPageLoadTime(),
            }
        };

        this.sendEvent(eventData);
    }

    /**
     * Track form interactions
     */
    trackFormInteraction(form, action) {
        const eventData = {
            event_type: 'form_interaction',
            page_url: window.location.href,
            page_title: document.title,
            event_timestamp: new Date().toISOString(),
            event_data: {
                form_id: form.id,
                form_class: form.className,
                form_action: action,
                form_method: form.method,
                field_count: form.elements.length,
            }
        };

        this.sendEvent(eventData);
    }

    /**
     * Track product interactions
     */
    trackProductInteraction(productId, action, additionalData = {}) {
        const eventData = {
            event_type: 'product_interaction',
            page_url: window.location.href,
            page_title: document.title,
            event_timestamp: new Date().toISOString(),
            event_data: {
                product_id: productId,
                action: action, // view, add_to_cart, add_to_wishlist, etc.
                ...additionalData,
            }
        };

        this.sendEvent(eventData);
    }

    /**
     * Track search queries
     */
    trackSearch(query, resultsCount = null) {
        const eventData = {
            event_type: 'search',
            page_url: window.location.href,
            page_title: document.title,
            event_timestamp: new Date().toISOString(),
            event_data: {
                search_query: query,
                results_count: resultsCount,
                search_type: 'product', // or 'blog', 'general', etc.
            }
        };

        this.sendEvent(eventData);
    }

    /**
     * Track custom events
     */
    trackCustomEvent(eventName, eventData = {}) {
        const event = {
            event_type: 'custom',
            page_url: window.location.href,
            page_title: document.title,
            event_timestamp: new Date().toISOString(),
            event_data: {
                custom_event_name: eventName,
                ...eventData,
            }
        };

        this.sendEvent(event);
    }

    /**
     * Send event to backend
     */
    async sendEvent(eventData) {
        if (!this.isTracking) return;

        try {
            const payload = {
                user_id: this.userId,
                session_id: this.sessionId,
                ip_address: await this.getClientIP(),
                ...eventData,
            };

            // Send to backend
            await fetch('/api/admin/analytics/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                },
                body: JSON.stringify(payload),
            });

            // Store locally for offline tracking
            this.events.push(payload);
            this.saveEventsLocally();

        } catch (error) {
            console.error('Analytics tracking error:', error);
            // Store event locally for retry later
            this.events.push(eventData);
            this.saveEventsLocally();
        }
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Click tracking
        document.addEventListener('click', (event) => {
            const element = event.target;
            if (element.getAttribute('data-track') || element.tagName === 'A' || element.tagName === 'BUTTON') {
                this.trackClick(element, event);
            }
        });

        // Scroll tracking (throttled)
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                this.trackScroll();
            }, 1000);
        });

        // Form tracking
        document.addEventListener('submit', (event) => {
            this.trackFormInteraction(event.target, 'submit');
        });

        document.addEventListener('focus', (event) => {
            if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') {
                this.trackFormInteraction(event.target.form, 'focus');
            }
        }, true);

        // Product interaction tracking
        document.addEventListener('click', (event) => {
            const element = event.target;
            const productId = element.getAttribute('data-product-id');
            const action = element.getAttribute('data-action');
            
            if (productId && action) {
                this.trackProductInteraction(productId, action);
            }
        });

        // Search tracking
        const searchForms = document.querySelectorAll('form[data-track="search"]');
        searchForms.forEach(form => {
            form.addEventListener('submit', (event) => {
                const query = form.querySelector('input[name="q"], input[name="search"]')?.value;
                if (query) {
                    this.trackSearch(query);
                }
            });
        });
    }

    /**
     * Setup page unload tracking
     */
    setupPageUnload() {
        window.addEventListener('beforeunload', () => {
            this.trackTimeOnPage();
            this.sendPendingEvents();
        });

        // For mobile devices
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                this.trackTimeOnPage();
            }
        });
    }

    /**
     * Setup visibility change tracking
     */
    setupVisibilityChange() {
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.pageStartTime = Date.now();
            } else {
                this.trackTimeOnPage();
            }
        });
    }

    /**
     * Get page load time
     */
    getPageLoadTime() {
        if (window.performance && window.performance.timing) {
            const timing = window.performance.timing;
            return timing.loadEventEnd - timing.navigationStart;
        }
        return null;
    }

    /**
     * Get client IP (simplified)
     */
    async getClientIP() {
        try {
            const response = await fetch('https://api.ipify.org?format=json');
            const data = await response.json();
            return data.ip;
        } catch (error) {
            return null;
        }
    }

    /**
     * Save events locally for offline tracking
     */
    saveEventsLocally() {
        const storedEvents = JSON.parse(localStorage.getItem('analytics_events') || '[]');
        storedEvents.push(...this.events);
        
        // Keep only last 100 events
        if (storedEvents.length > 100) {
            storedEvents.splice(0, storedEvents.length - 100);
        }
        
        localStorage.setItem('analytics_events', JSON.stringify(storedEvents));
        this.events = [];
    }

    /**
     * Send pending events
     */
    async sendPendingEvents() {
        const storedEvents = JSON.parse(localStorage.getItem('analytics_events') || '[]');
        
        for (const event of storedEvents) {
            try {
                await fetch('/api/admin/analytics/track', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    },
                    body: JSON.stringify(event),
                });
            } catch (error) {
                console.error('Failed to send pending event:', error);
                break; // Stop trying if we can't send
            }
        }
        
        // Clear sent events
        localStorage.removeItem('analytics_events');
    }

    /**
     * Enable/disable tracking
     */
    setTracking(enabled) {
        this.isTracking = enabled;
        localStorage.setItem('analytics_tracking_enabled', enabled);
    }

    /**
     * Check if tracking is enabled
     */
    isTrackingEnabled() {
        return localStorage.getItem('analytics_tracking_enabled') !== 'false';
    }
}

// Initialize analytics tracking
if (typeof window !== 'undefined') {
    window.AnalyticsTracker = new AnalyticsTracker();
    
    // Make it globally available
    window.trackEvent = (eventName, data) => window.AnalyticsTracker.trackCustomEvent(eventName, data);
    window.trackProduct = (productId, action, data) => window.AnalyticsTracker.trackProductInteraction(productId, action, data);
    window.trackSearch = (query, resultsCount) => window.AnalyticsTracker.trackSearch(query, resultsCount);
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AnalyticsTracker;
}
