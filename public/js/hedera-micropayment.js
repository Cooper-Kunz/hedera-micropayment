// by default, wordpress bundles with jQuery.
// write all our js logic for our admin page within this function.
(function( $ ) {
  'use strict';
  $(document).ready(function() {

    function detect(extensionId, notInstalledCallback, installedCallback) {
      var img = new Image();
      img.onerror = notInstalledCallback
      img.onload = installedCallback
      img.src = 'chrome-extension://' + extensionId + '/icons/icon16.png';
    }

    var hederaExtensionId = ajax_var.extension_id
    console.log('hedera-browser-extension id:', hederaExtensionId);

    function notInstalledCallback() {
      console.log('detect: user does not have extension installed');
    }

    function installedCallback() {
      console.log('detect: user has extension installed')
    }

    detect(hederaExtensionId, notInstalledCallback, installedCallback)

    var anonParam = new URLSearchParams(window.location.search)
    var anonId = anonParam.get('anon_id')
    appendAnonId(anonId);
    var url = "/?rest_route=/hedera-micropayment/v1/hash"
    console.log('url:', url)
    console.log('anonId:', ajax_var.anon_id)
    console.log('nonce:', ajax_var.nonce)
    var nonce = ajax_var.nonce
    if (window.requestIdleCallback) {
      requestIdleCallback(function () {
            Fingerprint2.getV18((hash) => {
              console.log('hash:', hash)
              var data = JSON.stringify({ "hash": hash, "anonId": anonId })
              $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: "json",
                contentType: "application/json;charset=utf-8",
                headers: {
                  "Authorization": nonce
                }
              })
              .done(function(data) {
                console.log('data', data)
              })
              .fail(function(jqXR, textStatus, error) {
                console.log('jqXR', jqXR)
                console.log('textStatus', textStatus)
                console.log('error', error)
              });
            })
      })
    } else {
      setTimeout(function () {
        Fingerprint2.getV18((result) =>  {
            console.log(result) // a hash generated from array of components
          })  
      }, 500)
    }

    function appendAnonId(anonId) {
      if (anonId) {
        var querystring = 'anon_id=' + anonId;
        $('a').each(function() {
          var href = $(this).attr('href');
          if (href) {
            if (href.match('#')) return;
            var hrefParams = new URLSearchParams(href);
            if (hrefParams.get('anon_id')) return;
            href += (href.match(/\?/) ? '&' : '?') + querystring;
            $(this).attr('href', href);
          }
        });
      }
    } 
  
  });
})( jQuery );
