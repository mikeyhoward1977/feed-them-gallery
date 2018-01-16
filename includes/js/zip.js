function ft_gallery_create_zip(e,t,a,l){jQuery(this);jQuery(".ft_gallery_download_button").attr("disabled","").addClass("ft_gallery_download_button_loading");var r=[];return jQuery("#tab-content1 section input[type=checkbox]").each(function(){jQuery(this).attr("checked")&&r.push(jQuery(this).attr("rel"))}),r.length&&(r=JSON.stringify(r)),jQuery.ajax({data:{action:"ft_gallery_create_zip_ajax",postId:e,ActivateDownload:t,CreateWooProd:a,DownloadNewestZIP:l},type:"POST",async:!0,url:ftgalleryAjax.ajaxurl,error:function(e,t,a){alert(e.responseText),alert(a)},xhr:function(){var e=new window.XMLHttpRequest;return e.addEventListener("progress",function(e){if(e.lengthComputable){var t=e.loaded/e.total;jQuery(".ft-gallery-notice").html(Math.round(100*t)+"%")}},!1),e},beforeSend:function(){jQuery(".ft-gallery-notice").empty().removeClass("ftg-block"),jQuery(".ft-gallery-notice").removeClass("updated").addClass("ftg-block"),jQuery(".ft-gallery-notice").prepend('<div class="fa fa-cog fa-spin fa-3x fa-fw ft-gallery-loader"></div><div>This may take a few minutes based on your gallery size and server speed.</div>')},success:function(e){return jQuery("#loading").hide(),jQuery(".ft-gallery-notice").removeClass("ftg-block"),console.log("Well Done and got this from sever: "+e),"false"==e||"yes"!=t&&"yes"!=l?"yes"!==t&&"yes"!==l&&"yes"==a?(jQuery(".ft-gallery-notice").html('ZIP Created! You can view it in the <a href="'+window.location.href+'&tab=ft_zip_gallery">ZIPs tab</a>. The Woocommerce product was also created you view it on the <a href="edit.php?post_status=publish&post_type=product&orderby=menu_order+title&order=ASC" target="_blank">Products Page</a>.'),jQuery(".ft-gallery-notice").prepend('<div class="fa fa-check-circle fa-3x fa-fw ft-gallery-success" ></div>'),jQuery(".ft-gallery-notice").addClass("updated")):(jQuery(".ft-gallery-notice").html('ZIP Created! You can view it in the <a href="'+window.location.href+'&tab=ft_zip_gallery">ZIPs tab</a>.'),jQuery(".ft-gallery-notice").prepend('<div class="fa fa-check-circle fa-3x fa-fw ft-gallery-success" ></div>'),jQuery(".ft-gallery-notice").addClass("updated")):window.location.assign(e),!1}}),!1}function ft_gallery_view_zip_contents(e,t,a){return jQuery.ajax({data:{action:"ft_gallery_view_zip_ajax",postId:e,ZIP_name:a,ZIP_ID:t},type:"POST",async:!1,url:ftgalleryAjax.ajaxurl,beforeSend:function(){jQuery("#ft-gallery-zip-list li.zip-list-item-"+t+" ol.zipcontents_list").empty(),jQuery("#ft-gallery-zip-list li.zip-list-item-"+t+" ol.zipcontents_list").append('<div class="fa fa-cog fa-spin fa-3x fa-fw ft-gallery-loader"></div>')},success:function(e){return jQuery("#ft-gallery-zip-list li.zip-list-item-"+t+" .ft_gallery_hide_zip_list").show(),jQuery("#ft-gallery-zip-list li.zip-list-item-"+t+" .zipcontents_list").show(),jQuery("#ft-gallery-zip-list li.zip-list-item-"+t+" .ft_gallery_view_zip_button").hide(),jQuery("#ft-gallery-zip-list li.zip-list-item-"+t+" ol.zipcontents_list").html(e),!1},error:function(e){alert("There was an error. Try again please!")}}),!1}function ft_gallery_hide_zip_contents(e,t){return jQuery("#ft-gallery-zip-list li.zip-list-item-"+t+" ol").hide(),jQuery("#ft-gallery-zip-list li.zip-list-item-"+t+" .ft_gallery_hide_zip_list").hide(),jQuery("#ft-gallery-zip-list li.zip-list-item-"+t+" .ft_gallery_view_zip_button").show(),!1}function ft_gallery_delete_zip(e,t){return confirm("Are you sure you want to delete this ZIP? This cannot be undone!")?(jQuery.ajax({data:{action:"ft_gallery_delete_zip_ajax",ZIP_ID:e},type:"POST",async:!1,url:ftgalleryAjax.ajaxurl,beforeSend:function(){jQuery(".ft-gallery-notice").empty(),jQuery(".ft-gallery-notice").removeClass("updated"),jQuery(".ft-gallery-notice").prepend('<div class="fa fa-cog fa-spin fa-3x fa-fw ft-gallery-loader"></div>')},success:function(t){return jQuery(".ft-gallery-file-delete").removeAttr("disabled").removeClass("ft_gallery_download_button_loading"),console.log("Well Done and got this from sever: "+t),jQuery("#ft-gallery-zip-list li.zip-list-item-"+e).remove(),jQuery(".ft-gallery-notice").html("ZIP Deleted! "),jQuery(".ft-gallery-notice").append('<div class="fa fa-check-circle fa-3x fa-fw ft-gallery-success" ></div>'),jQuery(".ft-gallery-notice").is(":empty")&&jQuery(".ft-gallery-notice").html('You have not created any ZIPs yet. You can do so from the <a href="'+window.location.href+"&tab=ft_images>Images tab</a>. "),jQuery(".ft-gallery-notice").addClass("updated"),!1},error:function(e){alert("There was an error. Try again please!")}}),!1):void 0}