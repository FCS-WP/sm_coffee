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
window.addEventListener("DOMContentLoaded",e=>{!function(){const e=document.querySelectorAll(".mdp-selection-grid"),t=window.innerWidth>1280;for(const o of e){const e=o.querySelectorAll(".mdp-selection-widget"),n=e.length/3-Math.floor(e.length/3)>.65;if(t&&n){const e=document.createElement("div");e.className="mdp-selection-widget mdp-empty",o.appendChild(e)}}}(),function(e){let t=jQuery("#mdp_custom_css_fld");if(!t.length)return;if(!wp.codeEditor)return;let o,n=wp.codeEditor.defaultSettings?_.clone(wp.codeEditor.defaultSettings):{};n.codemirror=_.extend({},n.codemirror,{indentUnit:2,tabSize:2,mode:"css"}),o=wp.codeEditor.initialize("mdp_custom_css_fld",n),o.codemirror.on("change",(function(e){o.codemirror.save(),t.change()}))}()});