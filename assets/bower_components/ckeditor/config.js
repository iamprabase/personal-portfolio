/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

//CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
//};

CKEDITOR.editorConfig = function( config )
{
    config.toolbar = 'MyToolbar';

    config.toolbar_MyToolbar =
    [
    
    ['Cut','Copy','Paste','PasteText','PasteFromWord','-', 'SpellChecker', 'Scayt'],
    ['Undo','Redo'],

    ['Bold','Italic','Underline'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
     ['Smiley','SpecialChar'],

    ['Font','FontSize'],
    ['TextColor','Maximize'],
 
    ];
    config.contentsCss = '/assets/bower_components/ckeditor/fonts.css'; 
	config.font_names = 'Preeti/Preeti;' + config.font_names; 

};
