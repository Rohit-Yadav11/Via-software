/*
 * Simple State/Province Select plugin for jQuery
 * 
 * Example:
 * $(document).ready(function() {
 *    $('#country').linkToStates('#state');
 *  });
 *
 * Copyright (c) 2008 Adam Daniels
 * Licensed under the MIT License
 *
 */

;(function($) {
  var countries = [], states = [], countryOptions ="<option value=''>--- Select Country ---</option>",  stateOptions ="", countrySelect, stateSelect, cc;
    
  var dataurl =  $('script[src$="jquery.address.js"]').attr('src').replace('jquery.address.js','')+"data/";

  $.fn.extend({
		linkStates: function(state_select_id) {
            $(this).html(countryOptions);
            stateSelect = $(state_select_id);
            countrySelect = $(this);
            $.getJSON(dataurl+'countries.json', function(data){
                $.each(data, function(i, country){
                    countries[""+country.code] = country.filename;
                    countryOptions += "<option value='"+country.code+"'>"+country.name+"</option>";
                });
                countrySelect.html(countryOptions);
            }); 
              $(this).change(function() {
                  var countryCode = $(this).val();
                  loadStates(countryCode);
                  
              });
        },      
  });
        function loadStates(countryCode){
              var filename = countries[countryCode];
              $.getJSON(dataurl+'countries/'+filename+'.json', function(data){
                    stateOptions = "";
                    $.each(data, function(i, state){
                        states[""+state.code] = state;
                        var scode = state.code.replace(countryCode+"-", "");
                        stateOptions += "<option value='"+scode+"'>"+state.name+"</option>";
                    });
                    stateSelect.html(stateOptions);
                });

        }
})(jQuery);