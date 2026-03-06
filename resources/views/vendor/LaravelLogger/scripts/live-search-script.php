<script src="{{ asset('backend/vendor/axios/axios.min.js') }}"></script>
<script src="{{ asset('backend/vendor/jquery/jquery.slim.min.js') }}"></script>
<script type="text/javascript">
    // Script for submitting the livesearch via axios
  $( "#live_search_button" ).on( "click", function() {
    axios.post('/activity/live-search', {
      userid: document.getElementById('live_search_userid').value,
      email: document.getElementById('live_search_email').value
    })
    .then(function (response) {
        var newOptions = response.data;
        // console.log(newOptions)
        var $el = $("#user_select");
        $el.empty(); // remove old options
        $.each(newOptions, function(key,value) {
          $el.append($("<option></option>")
             .attr("value", key).text(value));
        });
        // console.log(response);
    })
    .catch(function (error) {
        console.log(error);
    });
});

</script>

