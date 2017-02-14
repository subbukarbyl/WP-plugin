jQuery(document).ready(function($){
var reset_link, link_url, termArray=Array();
var urla=window.location.href;
if($('#buildkar_form input[type=checkbox]').length==0){$('.brands-widget').css('display','none');}
$('#buildkar_form .brands_search').on('keyup', function() { var query = this.value.toLowerCase();
$('[id^="id"]').each(function(i, elem){ if (elem.name.toLowerCase().indexOf(query) != -1) {$(this).parent('li.checkbox_dv').css('display','block');}
else{$(this).parent('li.checkbox_dv').css('display','none');} }); });
//More button
var qty = $('input[type=checkbox]').length-6 + ' More Brands';
if(qty!='0 More' && $('input[type=checkbox]').length>6){$('#buildkar_form .more_buttn').html( '+' + qty).css('display', 'block');}
$('span.more_buttn').click(function(){ $('.fltr_dv').css('overflow-y','scroll');
$('#buildkar_form input[type=checkbox]').parent('li.checkbox_dv').css('display','block');
$('#buildkar_form span.more_buttn').hide(); });
//Reset button
$('.reset_buttn').click(function(){ localStorage.clear('checkboxValues');
window.location=window.location.href.substr(0,window.location.href.indexOf("?swoof")); });
//storing checked checkbox ids to localstorage
var checkboxValues = JSON.parse(localStorage.getItem('checkboxValues')) || {};
var $checkboxes = $('#buildkar_form input[type=checkbox]');
if($checkboxes.length<=6){ $('span.more_buttn').hide(); }//new
//when checkbox checked
$checkboxes.change(function(){ termArray=[];
$('.brands-widget .fltr_dv').prepend('<div class="img_load"></div>');
var dv_height=$('.brands-widget').innerHeight(); 
$('.img_load').css('height',dv_height);
checkboxValues[this.id] = this.checked;
localStorage.setItem('checkboxValues', JSON.stringify(checkboxValues));
//Creating array of checked checkbox elements
$('#buildkar_form input[type=checkbox]:checked').each(function(){ termArray.push($(this).val()); });
//If all checkboxes are unchecked
if(termArray.length==0){ var urs_str=window.location.href;
window.location.href=urs_str.substr(0,urs_str.indexOf("?swoof")); }
else{ var n=urla.indexOf('?swoof=');
if(n>0){urla="";}
window.location.href=urla + "?swoof=0&product_brand=" + termArray;}
});
//Display message if no products found
if(($('#content .content ul.products').length==0)&&(!$('#content .content p').hasClass('woocommerce-info'))){ $('#content .content').append('<p class="woocommerce-info">No products found which match your selection.</p>'); }
//On page load
$.each(checkboxValues, function(key, value) { $("#" + key).prop('checked', value);
$('#buildkar_form input[type=checkbox]:checked').parent('li.checkbox_dv').prependTo('ul.checkbox_cover'); });
$(window).unload(function(){
  localStorage.removeItem(checkboxValues);
});
});