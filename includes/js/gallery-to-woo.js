function ft_gallery_image_to_woo(e){var t=[];return jQuery("#tab-content1 section li div.ft-gallery-select-thumbn input[type=checkbox]").each(function(){jQuery(this).attr("checked")&&t.push(jQuery(this).attr("rel"))}),t.length&&(t=JSON.stringify(t)),jQuery.ajax({data:{action:"ft_gallery_image_to_woo_prod",GalleryID:e,selectedMedia:t},type:"POST",async:!0,url:ftgallerytoWooAjax.ajaxurl,beforeSend:function(){jQuery(".ft-gallery-notice").empty().removeClass("ftg-block"),jQuery(".ft-gallery-notice").removeClass("updated").addClass("ftg-block"),jQuery(".ft-gallery-notice").prepend('<div class="fa fa-cog fa-spin fa-3x fa-fw ft-gallery-loader"></div>')},success:function(e){return console.log("Well Done and got this from sever: "+e),jQuery(".ft-gallery-notice").html(e),jQuery(".ft-gallery-notice").addClass("updated"),jQuery(".ft_gallery_download_button").removeAttr("disabled").removeClass("ft_gallery_download_button_loading"),!1}}),!1}function ft_gallery_zip_to_woo(e,t){return jQuery.ajax({data:{action:"ft_gallery_zip_to_woo_prod",GalleryID:e,ZIP_ID:t},type:"POST",async:!0,url:ftgallerytoWooAjax.ajaxurl,beforeSend:function(){jQuery(".ft-gallery-notice").empty().removeClass("ftg-block"),jQuery(".ft-gallery-notice").removeClass("updated").addClass("ftg-block"),jQuery(".ft-gallery-notice").prepend('<div class="fa fa-cog fa-spin fa-3x fa-fw ft-gallery-loader"></div>')},success:function(e){return console.log("Well Done and got this from sever: "+e),jQuery(".ft-gallery-notice").html(e),jQuery(".ft-gallery-notice").addClass("updated"),jQuery(".ft_gallery_download_button").removeAttr("disabled").removeClass("ft_gallery_download_button_loading"),!1}}),!1}