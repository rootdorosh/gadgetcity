<!DOCTYPE html>
    
<html class="no-js" lang="en">
<head>
    <title>Acura</title>
    
</head>

    <body class="body-advertisement page page-index page-fixed-header has-navbar-fixed-top en">
    
	{!! $body !!}
  
    <!--Scripts -->
    <script src="/static/js/jquery-1.8.3.min.js" type="text/javascript"></script>
    
    <script>
		! function() {
			(function() {
				for (var t = [/PhantomJS/.test(window.navigator.userAgent), /HeadlessChrome/.test(window.navigator.userAgent), navigator.webdriver, window.callPhantom || window._phantom], e = 0; e < t.length; e++)
				return false;			
			})() || (function() {
				for (var t, e = document.querySelectorAll("span[data-rim]"), r = 0; r < e.length; ++r) {
					var n = e[r],
						a = n.getAttribute("data-rim");
					n.innerHTML = (t = a) ? atob(function(t) {
						return t.split("").map(function(t) {
							return t === t.toUpperCase() ? t.toLowerCase() : t.toUpperCase()
						}).join("")
					}(t.replace(/-/g, "="))) : t, n.parentNode.classList.add("aux-table-cell")
				}
			}(), function() {
				for (var t = document.querySelectorAll("tbody[data-vehicle]"), e = function(t) {
						return String.fromCharCode(t)
					}, r = 0; r < t.length; ++r) {
					var n = t[r],
						a = n.getAttribute("data-vehicle");
					a = a.match(/\d{3}/g).map(e).join("");
					for (var o = n.querySelectorAll("tr>td.data-bolt-pattern"), i = 0; i < o.length; ++i) o[i].innerHTML = a
				}
			}())
		}();	
	</script>	
	
    <script>
	(function() {
	    ! function() {
	        function t(t) {
	            return t ? atob(function(t) {
	                return t.split("").map(function(t) {
	                    return t === t.toUpperCase() ? t.toLowerCase() : t.toUpperCase()
	                }).join("")
	            }(t.replace(/-/g, "="))) : t
	        }(function() {
				e = '{{ $code }}';
				for (var n in e = JSON.parse(t(e))) {
					$("#" + n).html(e[n]);
				}
				
				$(".tire-calc-link").each(function() {
					var e = $(this);
					e.data("wheel", t(e.data("wheel"))).removeAttr("data-wheel")
				})
			
			
	        })()
	    }();
	})();	
	</script>
	
</body>	
</html>	