/**
 * @brief  slide show javascript
 * @author Daijiro Abe
 * @date   2007.07.22
 */

var slide_id = 'slideshow';
var img_base = '/tol/bimg/';
var img_width = 729;
var img_height = 206;
var img_ext = ".jpg";
var interval = 5;
var fadeColor = "#ffffff";

function autoexec()
{
  var e = $(slide_id);
  var classes_str = e.className;
  classes_str = classes_str.replace(/^\s+|\s+$/g, "");
  var classes = classes_str.split(" ");
  
  var delay_sec = interval+(interval+2)*(classes.length-1);
  var white = document.createElement("div");
  white.id = "slideshow_white";
  white.style.backgroundColor = fadeColor;
  white.style.width = img_width;
  white.style.margin = 0;
  white.style.padding = 0;
  white.style.zIndex = 10;
  white.style.position = "relative";
  white.style.top = "-" + img_height + "px";
  white.style.height = img_height + "px";
  white.style.lineHeight = img_height + "px";
  e.appendChild(white);

  new Effect.Fade(white.id, {
  		from: 1.0,
  		to: 0.0,
  		delay: delay_sec,
  		fps: 60,
  		duration: 2,
			afterFinishInternal: function(effect) {
			}
  });

  for(var i = 0; i < classes.length; i++)
  {
    var div = document.createElement("div");
    var img = document.createElement("img");
    img.src = img_base + classes[i] + img_ext;
    div.appendChild(img);
    div.id = "slideshow_" + classes[i];
    div.style.margin = 0;
    div.style.padding = 0;
    div.style.zIndex = (i+1)*100;
    div.style.position = "relative";
    div.style.top = "-" + (i+2)*img_height + "px";
    div.style.height = img_height + "px";
    div.style.lineHeight = img_height + "px";
    e.appendChild(div);
    
    var delay_sec = interval+(interval+2)*i;
    
    new Effect.Fade(div.id, {
    		from: 1.0,
    		to: 0.0,
    		delay: delay_sec,
    		fps: 60,
    		duration: 2,
				afterFinishInternal: function(effect) {
					if(effect.element.id == "slideshow_" + classes[classes.length-1])
					{
						for(var j = 0; j < classes.length; j++)
						{
							$('slideshow_' + classes[j]).style.display = "none";
							$('slideshow_white').style.display = "none";
						}
					}
				}
    });
    
    if(i > 0)
    {
			div.style.display = "none";
			var delay_sec2 = interval+(interval+2)*(i-1);
			new Effect.Appear(div.id, {
				from: 0.0,
				to: 1.0,
				delay: delay_sec2,
				fps: 60,
				duration: 2
			});
		}
  }
}

Event.observe(window,"load",autoexec);
