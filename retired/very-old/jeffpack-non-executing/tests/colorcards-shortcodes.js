/*! from Colorcards by Jeff-Russ http://github.com/Jeff-Russ/ */

///////////////////////////////////////////////////////////////////////////////
/* ready() is similar to jQuery.ready() and here it's without comments and 
had a lot of whitespace removed. for more info it's from Timo Huovinen's reply:
http://stackoverflow.com/questions/799981/document-ready-equivalent-without-jquery */
var ready = ( function() {
  var readyList, DOMContentLoaded, class2type = {};
    class2type["[object Boolean]"] = "boolean";
    class2type["[object Number]"] = "number";
    class2type["[object String]"] = "string";
    class2type["[object Function]"] = "function";
    class2type["[object Array]"] = "array";
    class2type["[object Date]"] = "date";
    class2type["[object RegExp]"] = "regexp";
    class2type["[object Object]"] = "object";

  var ReadyObj = { isReady: false, readyWait: 1,
    holdReady: function(hold) {
      if (hold) ReadyObj.readyWait++;
      else ReadyObj.ready( true );
    },
    ready: function( wait ) {
      if ( ( wait === true && !--ReadyObj.readyWait ) || ( wait !== true && !ReadyObj.isReady ) ) {
        if ( !document.body ) return setTimeout( ReadyObj.ready, 1 );
        ReadyObj.isReady = true;
        if ( wait !== true && --ReadyObj.readyWait > 0 ) return;
        readyList.resolveWith( document, [ReadyObj] );
      }
    },
    bindReady: function() {
      if (readyList) return;
      readyList = ReadyObj._Deferred();
      if ( document.readyState === "complete" ) return setTimeout( ReadyObj.ready, 1 );
      if ( document.addEventListener ) {
        document.addEventListener( "DOMContentLoaded", DOMContentLoaded, false );
        window.addEventListener( "load", ReadyObj.ready, false );
      } else if ( document.attachEvent ) {
        document.attachEvent( "onreadystatechange", DOMContentLoaded );
        window.attachEvent( "onload", ReadyObj.ready );
        var toplevel = false;
        try { toplevel = window.frameElement == null; } catch( e ) {}
        if ( document.documentElement.doScroll && toplevel ) doScrollCheck();
      }
    },
    _Deferred: function() {
      var callbacks = [], fired, firing, cancelled,
        deferred  = {
          done: function() {
            if (!cancelled) {
              var args = arguments, i, length, elem, type, _fired;
              if ( fired ) { _fired = fired; fired = 0; }
              for ( i = 0, length = args.length; i < length; i++ ) {
                elem = args[i];
                type = ReadyObj.type( elem );
                if ( type === "array" )  deferred.done.apply( deferred, elem );
                else if ( type === "function" ) callbacks.push( elem );
              }
              if ( _fired ) deferred.resolveWith( _fired[0], _fired[1] );
            }
            return this;
          },
          resolveWith: function( context, args ) {
            if ( !cancelled && !fired && !firing ) {
              args = args || [];
              firing = 1;
              try { while( callbacks[0] ) { callbacks.shift().apply( context, args ); } }
              finally { fired = [context, args]; firing = 0; }
            }
            return this;
          },
          resolve: function() { deferred.resolveWith( this, arguments ); return this; },
          isResolved: function() { return !!( firing || fired ); },
          cancel: function() { cancelled = 1; callbacks = []; return this; }
        };
      return deferred;
    },
    type: function(obj) {
      return obj == null ? String(obj) : class2type[Object.prototype.toString.call(obj)] || "object";
    }
  }
  function doScrollCheck() {
    if ( ReadyObj.isReady ) return;
    try { document.documentElement.doScroll( "left" ); }
    catch( e ) { setTimeout( doScrollCheck, 1 ); return; }
    ReadyObj.ready();
  }
  if ( document.addEventListener ) {
    DOMContentLoaded = function() {
      document.removeEventListener( "DOMContentLoaded", DOMContentLoaded, false );
      ReadyObj.ready();
    };

  } else if ( document.attachEvent ) {
    DOMContentLoaded = function() {
      if ( document.readyState === "complete" ) {
        document.detachEvent( "onreadystatechange", DOMContentLoaded );
        ReadyObj.ready();
      }
    };
  }
  function ready( fn ) {
    ReadyObj.bindReady(); var type = ReadyObj.type( fn ); readyList.done( fn );
  }
  return ready;
} )();
// END ready()

///////////////////////////////////////////////////////////////////////////////

// VARIOUS FUNCTIONS ( usable in other projects ):

function hasClass( element, cls ) { // check if JS element has class (string)
  return ( ' ' + element.className + ' ' ).indexOf( ' ' + cls + ' ' ) > -1;
}

function checkHash() { // get element pointed to by hash
  if ( // if we have a hash that targets an element's location 
       location.hash.length > 1  && location.hash[0] === "#" 
       && document.getElementById( location.hash.substr( 1 ) )
     ){
    // return element pointed to by hash
    return document.getElementById( location.hash.substr( 1 ) ); // or...
  } else {return false;} // so we can check the status of call to checkHash()
}

function addChecked(checkbox) { // cross browser way to check a checkbox
  checkbox.setAttribute( "checked", "checked" );
  checkbox.checked = true; // failsafe for IE
}
function removeChecked(checkbox) { // cross browser way to uncheck a checkbox
  checkbox.setAttribute( "checked", "" ); // For IE 
  checkbox.removeAttribute( "checked" );  // For other browsers
  checkbox.checked = false; 
}

// VARIOUS FUNCTIONS for this project:

function collapseAll() { // run removeChecked() on all within .collapsible
  var all_collapsibles = document.getElementsByClassName('collapsible');
  for ( var i = 0; i < all_collapsibles.length; i++ ) {
    var inputs = all_collapsibles[i].getElementsByTagName('input');
    if ( inputs && inputs[0].type.toLowerCase() == "checkbox" ) {
      removeChecked( inputs[0] );
    }
  }
}
function collapseFromHash(hash_target) {
  var result = "not collapse hash";
  if ( hash_target /* would be false if not pointing to existing id */
       && hasClass( hash_target, "collapsible" ) 
       && hash_target.getElementsByTagName('input')
       && hash_target.getElementsByTagName('input')[0]
                     .type.toLowerCase() == "checkbox"
     ) {
    collapseAll();
    addChecked( hash_target.getElementsByTagName('input')[0] );
    hash_target.scrollIntoView();
    result = "collapse hash found";
  }
  return result;
}
/*
function collapseDefault() {
  if ( document.getElementsByClassName('collapsible')
       && document.getElementsByClassName('collapsible')[0]
                  .getElementsByTagName('input') ) {
    var first = document.getElementsByClassName('collapsible')[0]
                        .getElementsByTagName('input')[0];
    if ( first.type.toLowerCase() == "checkbox" ) addChecked( first );
  }
}
*/
function configureCollapse () {
  var hash_target = checkHash();
  if (hash_target) collapseFromHash(hash_target);
}

////////////////////////////////////////////////////////////////////////////////
// note that ready() seems to not be working in codepen
ready( function() {
  window.onhashchange = configureCollapse;

  // var configureCollapse = function() {
  //   var hash_target = checkHash();
  //   if (hash_target) collapseFromHash(hash_target);
  // }
  // var collapseFromHash = function() {
  //   var cc_d_var = setInterval( function() {
  //     clearInterval( cc_d_var ); 
  //     var hash_target = checkHash(); 
  //     var result = collapseFromHash(hash_target);
  //     if ( result == "not collapse hash" ) collapseDefault();
  //   }, 100 );
  // }
  
  /****************** some extra stuff if we have jQuery ********************/
  
  if (window.jQuery ){//testing for jQuery this way not working in codepen
  // if (typeof jQuery != 'undefined') {// another way

    // restore all checkbox states between sessions with cookies:
    $('input[id^=checkbox-]').each( function() {
      var mycookie = $.cookie( $(this).attr('id') );
      if (mycookie && mycookie == "true") $(this).prop('checked', mycookie);
    });
    // save all checkbox states between sessions with cookies:
    $('input[id^=checkbox-]').change( function() {
      $.cookie ( $(this).attr('id'),
                 $(this).prop('checked'),
                 { path: '/', expires: 365 }
               );
    });

  }/*********** END some extra stuff if we have jQuery ***********************/

});

////////////////////////////////////////////////////////////////////////////
// FOR DEMO //
if (typeof jQuery != 'undefined') { 
  $('.test-content').parent().addClass("test-body");
}

