elevatezoom
===========

This plugin adds elevateZoom support to WordPress.

For more about elevateZoom see http://www.elevateweb.co.uk/image-zoom.

##Installing the plugin 

1. Download the zip file (see button in right-hand margin >) and upload via the new plugin screen in your WordPress admin interface. 
2. Review the settings at Settings > elevateZoom and change as necessary

##Adding elevateZoom to single images

To activate elevateZoom on an image you just have to give it a class of 'elevatezoom' (or whatever class you set in the settings).
However, the zoom will only work if a zoom image has been specified and there are 2 ways to do that:

1) When you add an image via the WordPress media dialog, select link to media file (but don't use a caption). This will output
HTML something like:
```
<a href="original.jpg"><img src="image.jpg"></a>
```
Just add the class 'elevatezoom' to the img element and the script will assume that the zoomed image is that referenced by the
parent a tag.

2) If you don't want to use the linking method, then you can specify the zoomed image using the data-zoom-image attribute:
```
<img src="image.jpg" class="elevatezoom" data-zoom-image="original.jpg">
```
##Adding elevateZoom to galleries

The plugin adds its own gallery shortcode, zoomgallery, which is really a cut-down version of the builtin version that writes
out HTML that is more appropriate for elevateZoom.

The best way to generate the zoomgallery shortcode is actually to create a normal WordPress gallery and then just edit the
name from gallery to zoomgallery. So, if you create a gallery that looks like this:
```
[gallery ids="1,2,3,4,5"]
```
then just change it to this:
```
[zoomgallery ids="1,2,3,4,5"]
```
###Gallery image sizes

The gallery image sizes are set in the Settings > elevateZoom page. The sizes are:

* thumbnail - the navigation / selection image
* display - the normal image that appears when it's thumbnail is clicked
* zoom - the image that provides the zoomed detail

**IMPORTANT!** If you are using the plugin on an existing site then previously uploaded images will not have versions of these
sizes so you will either need to use a plugin such as Regenerate Thumbnails or manually edit and resave the image.

###Adding Fancybox to galleries

elevateZoom integrates nicely with Fancybox, a Lightbox alternative and the plugin also supports this, so long as you have
installed a Fancybox plugin, such as Easy Fancybox (https://wordpress.org/plugins/easy-fancybox/). 

To enable your gallery images to be displayed in a lightbox on click, just add the attribute "data-fancybox" to the
zoomgallery shortcode with a value of "yes" :
```
[zoomgallery ids="1,2,3,4,5" data-fancybox="yes"]
```
##Overriding default settings on individual images & galleries

The default settings used by elevateZoom can be overridden on an image or gallery basis by specifying the setting locally.

For example, the default is for a window zoomType with a lensShape of square and a lensSize of 200. Let's say that for one
image you wanted to use a round lens zoomType with a size of 100:
```
<img src="image.jpg" data-zoom-type="lens" data-lens-shape="round" data-lens-size="100" data-zoom-image="original.jpg">
```
Or for a gallery:
```
[zoomgallery ids="1,2,3,4,5" data-zoom-type="lens" data-lens-shape="round" data-lens-size="100" data-fancybox="yes]
```
