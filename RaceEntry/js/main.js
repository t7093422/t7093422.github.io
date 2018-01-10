$(
  function() {

    function extractParams(uri) {
      var params = {};
      var query;
      if(typeof(uri) === 'string') {
        query = uri.split("?");
        query = query[1];
        if(query) {
          query = query.split("&");
          for(var keyval of query) {
            keyval = keyval.split("=");
            params[keyval[0]] = keyval[1];
          }
        }
      }
      return params;
    }

    $('body').
      on(
        'click',
        '.destroy',
        function(evt) {
          var params;
          evt.stopPropagation();
          evt.preventDefault();
          if(confirm("Are you sure?")) {
            params = extractParams($(this).prop('href'));
            $.post(
              $(this).prop('href'),
              params
            ).
            done(
              function() {
                alert("Record Deleted");
                window.location.reload();
              }
            );
          }
          return false;
        }
      );
  }
);