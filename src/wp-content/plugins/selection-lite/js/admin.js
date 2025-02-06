/**
 * Selection Lite
 * Carefully selected Elementor addons bundle, for building the most awesome websites
 *
 * @encoding        UTF-8
 * @version         1.15
 * @copyright       (C) 2018 - 2023 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         GPLv3
 * @contributors    merkulove, vladcherviakov, phoenixmkua, podolianochka, viktorialev01
 * @support         help@merkulov.design
 **/

window.addEventListener( 'DOMContentLoaded', (event) => {

    /**
     * Add to the grid third element if needed
     */
    function arrangeGrid() {

        const grids = document.querySelectorAll( '.mdp-selection-grid' );
        const isTriple = window.innerWidth > 1280;

        for ( const grid of grids ) {

            const gridItems = grid.querySelectorAll( '.mdp-selection-widget' );
            const twoInRow = ( ( gridItems.length / 3 ) - Math.floor( gridItems.length / 3 ) ) > 0.65;

            if ( isTriple && twoInRow ) {

                const emptyDiv = document.createElement( 'div' );
                emptyDiv.className = 'mdp-selection-widget mdp-empty';

                grid.appendChild( emptyDiv );

            }

        }

    }

    /** Init function */
    arrangeGrid();


    /** Custom CSS */
    function customCSSInit() {
        ( function ( $ ) {
            let $custom_css_fld = $('#mdp_custom_css_fld');

            if (!$custom_css_fld.length) {
                return;
            }

            if ( ! wp.codeEditor ) { return; }

            let editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror, {
                    indentUnit: 2,
                    tabSize: 2,
                    mode: 'css'
                }
            );

            let css_editor;
            css_editor = wp.codeEditor.initialize( 'mdp_custom_css_fld', editorSettings );

            css_editor.codemirror.on( 'change', function( cMirror ) {
                css_editor.codemirror.save(); // Save data from CodeEditor to textarea.
                $custom_css_fld.change();
            } );
        }( jQuery ) );
    }
    customCSSInit();

});
