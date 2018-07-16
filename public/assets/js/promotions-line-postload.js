jQuery(".details-toggle").click(function(e) {
    var t = jQuery(this);
    t.data("open") ? (t.next(".coupon-details").addClass("hide-details"), t.find("span i").removeClass("icon-arrow-up-blue"), t.find("span i").addClass("icon-arrow-down-blue"), t.data("open", !1)) : (t.next(".coupon-details").removeClass("hide-details"), t.find("span i").removeClass("icon-arrow-down-blue"), t.find("span i").addClass("icon-arrow-up-blue"), t.data("open", !0))
});
jQuery(document).ready(function() {
    jQuery("#coupon-popup").click(function(e) {
        clicked = false;
        if(clicked)
           return;
        clicked = true;
        jQuery(this).fadeOut('swing', function () {
            jQuery(this).remove();
        });

    });
    jQuery(".open-tab").click(function(e) {
        // e.preventDefault();
        var id = jQuery(this).data('idoffer');
        var url = document.URL;
        var vars = [];
        var root = url;
        params = {};
        if (url.indexOf('?') != -1  )
        {
            var hashes = url.slice(url.indexOf('?') + 1).split('&');
            root = url.slice(0, url.indexOf('?'));

            for(var i = 0; i < hashes.length; i++)
            {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            for(var i = 0; i < vars.length; i++)
            {
                params[vars[i]] = vars[vars[i]];
            }
        }
        params['popup-code'] = id;
        console.log(root + '?' + jQuery.param( params ));
        window.open(root + '?' + jQuery.param( params ));
    });
    jQuery(".copy-code-button").click(function(e){
        e.preventDefault();
        var aux = document.createElement("input");
        aux.setAttribute("value", jQuery(this).data('id') );
        console.log(jQuery(this).data('id'));
        document.body.appendChild(aux);
        aux.select();
        document.execCommand("copy");
        document.body.removeChild(aux);
        jQuery(this).html('Đã chép');
    });
    jQuery("#coupon-popup .inner").click(function(e){
        e.stopPropagation();
    });
});

