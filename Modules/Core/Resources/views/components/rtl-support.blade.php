@props(['isRTL', 'currentLocale'])

@if($isRTL)
<style>
/* RTL Support Styles */
html[dir="rtl"] {
    direction: rtl;
}

html[dir="rtl"] body {
    text-align: right;
}

/* RTL Layout Adjustments */
html[dir="rtl"] .container,
html[dir="rtl"] .container-fluid {
    direction: rtl;
}

/* RTL Navigation */
html[dir="rtl"] .navbar-nav {
    flex-direction: row-reverse;
}

html[dir="rtl"] .dropdown-menu {
    right: 0;
    left: auto;
}

/* RTL Forms */
html[dir="rtl"] .form-control {
    text-align: right;
}

html[dir="rtl"] .input-group {
    flex-direction: row-reverse;
}

html[dir="rtl"] .input-group-prepend {
    margin-left: -1px;
    margin-right: 0;
}

html[dir="rtl"] .input-group-append {
    margin-right: -1px;
    margin-left: 0;
}

/* RTL Buttons */
html[dir="rtl"] .btn-group > .btn:not(:last-child):not(.dropdown-toggle) {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

html[dir="rtl"] .btn-group > .btn:not(:first-child) {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

/* RTL Cards */
html[dir="rtl"] .card-header {
    text-align: right;
}

html[dir="rtl"] .card-body {
    text-align: right;
}

/* RTL Tables */
html[dir="rtl"] .table th,
html[dir="rtl"] .table td {
    text-align: right;
}

/* RTL Pagination */
html[dir="rtl"] .pagination {
    justify-content: flex-end;
}

/* RTL Breadcrumbs */
html[dir="rtl"] .breadcrumb-item + .breadcrumb-item::before {
    content: "\\";
    transform: scaleX(-1);
}

/* RTL Modals */
html[dir="rtl"] .modal-header {
    flex-direction: row-reverse;
}

html[dir="rtl"] .modal-footer {
    flex-direction: row-reverse;
}

/* RTL Language Switcher */
html[dir="rtl"] .language-switcher .dropdown-toggle {
    flex-direction: row-reverse;
}

html[dir="rtl"] .language-switcher .dropdown-item {
    flex-direction: row-reverse;
}

/* RTL Sidebar */
html[dir="rtl"] .sidebar {
    right: 0;
    left: auto;
}

html[dir="rtl"] .main-content {
    margin-right: 250px;
    margin-left: 0;
}

/* RTL Icons */
html[dir="rtl"] .fa-chevron-left::before {
    content: "\\f054";
}

html[dir="rtl"] .fa-chevron-right::before {
    content: "\\f053";
}

html[dir="rtl"] .fa-arrow-left::before {
    content: "\\f061";
}

html[dir="rtl"] .fa-arrow-right::before {
    content: "\\f060";
}

/* RTL Float */
html[dir="rtl"] .float-left {
    float: right !important;
}

html[dir="rtl"] .float-right {
    float: left !important;
}

/* RTL Text Alignment */
html[dir="rtl"] .text-left {
    text-align: right !important;
}

html[dir="rtl"] .text-right {
    text-align: left !important;
}

/* RTL Margin and Padding */
html[dir="rtl"] .ml-auto {
    margin-left: 0 !important;
    margin-right: auto !important;
}

html[dir="rtl"] .mr-auto {
    margin-right: 0 !important;
    margin-left: auto !important;
}

html[dir="rtl"] .pl-0 {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

html[dir="rtl"] .pr-0 {
    padding-right: 0 !important;
    padding-left: 0 !important;
}
</style>

<script>
// Set RTL direction on HTML element
document.documentElement.setAttribute('dir', 'rtl');
document.documentElement.setAttribute('lang', '{{ $currentLocale }}');
</script>
@else
<script>
// Set LTR direction on HTML element
document.documentElement.setAttribute('dir', 'ltr');
document.documentElement.setAttribute('lang', '{{ $currentLocale }}');
</script>
@endif
