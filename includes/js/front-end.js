function slickremixFTGalleryImageResizing(){var e=jQuery(".ft-wp-gallery-centered"),a=jQuery(".slicker-ft-gallery-placeholder"),r=e.attr("data-ftg-columns"),t=e.attr("data-ftg-margin"),l=2*parseFloat(t),n=e.width(),i=e.attr("data-ftg-force-columns");if("1"===r||"2"===r||"3"===r||"4"===r||"5"===r||"6"===r||"7"===r||"8"===r){if(n<="376"&&"no"===i)var s="calc(100% - "+l+"px)";else if(n<="736"&&"no"===i)s="calc(50% - "+l+"px)";else if("8"===r)s="calc(12.5% - "+l+"px)";else if("7"===r)s="calc(14.28571428571429% - "+l+"px)";else if("6"===r)s="calc(16.66666666666667% - "+l+"px)";else if("5"===r)s="calc(20% - "+l+"px)";else if("4"===r)s="calc(25% - "+l+"px)";else if("3"===r)s="calc(33.33333333333333% - "+l+"px)";else if("2"===r)s="calc(50% - "+l+"px)";else if("1"===r)s="calc(100% - "+l+"px)";a.css({width:s});var y=a.width();return a.css({width:s,height:y,margin:t}),!1}var c=e.attr("data-ftg-width")?e.attr("data-ftg-width"):"325px";return a.css({width:c,height:c,margin:t}),!1}jQuery(window).on("load",function(){jQuery(".ft-wp-gallery-masonry .ft-gallery-variations-text select").on("change",function(){jQuery(".ft-wp-gallery-masonry").masonry("reloadItems"),setTimeout(function(){jQuery(".ft-wp-gallery-masonry").masonry("layout")},200)}),jQuery.fn.masonry&&jQuery(".masonry").hasClass("ft-wp-gallery-masonry")&&(jQuery(".ft-wp-gallery-masonry").masonry(),setTimeout(function(){jQuery(".ft-wp-gallery-masonry").masonry("reloadItems"),jQuery(".ft-wp-gallery-masonry").masonry("layout")},600))}),jQuery(document).ready(function(){jQuery.fn.ftsShare=function(){jQuery(".fts-share-wrap").each(function(){var e=jQuery(this);e.find(".ft-gallery-link-popup").unbind().bind("click",function(){e.find(".ft-gallery-share-wrap").toggle()})})},jQuery.fn.ftsShare&&jQuery.fn.ftsShare()}),jQuery(document).ready(slickremixFTGalleryImageResizing),jQuery(window).on("resize",slickremixFTGalleryImageResizing);