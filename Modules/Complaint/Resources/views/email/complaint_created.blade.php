<h1>Complaint Created</h1>
<p>Complaint ID: {{ $complaint->id }}</p>
<p>Complaint: {{ $complaint->complaint }}</p>
<p>Status: {{ $complaint->status }}</p>

@if($recipientType === 'admin')
    <p>Order ID: {{ $complaint->order_id }}</p>
    <p>Created By: {{ $complaint->user->name }}</p>
@endif
