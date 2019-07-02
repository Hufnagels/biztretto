/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
if ( CKEDITOR ) {
        for ( name in CKEDITOR.instances ) {
            CKEDITOR.instances[name].destroy()
        }
    }
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	//config.uiColor = '#333333';
  config.language = 'en';
  config.toolbar = 'Extra';
  config.floatSpaceDockedOffsetY  = 15;
};
CKEDITOR.disableAutoInline = true;
// Turn off automatic editor creation first.
/*
CKEDITOR.inline( 'inline1', {
   extraPlugins: 'sharedspace',
   removePlugins: 'floatingspace,resize',
   sharedSpaces: {
      top: 'myNicPanel',
      bottom: 'bottom'
   }
});
*/