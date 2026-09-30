/* The theme's MenuDash blocks in the editor (inc/blocks.php): each shows a live preview
   drawn by the server from what is entered in MenuDash, and saves nothing but its name, so
   the page never keeps an old phone number or address. Names and the hint come translated
   from PHP (mdtBlocks). */
(function (blocks, element, blockEditor, ServerSideRender, data) {
  "use strict";
  var el = element.createElement;
  Object.keys(data.names).forEach(function (short) {
    var name = "menudash-theme/" + short;
    var settings = {
      apiVersion: 3,
      title: data.names[short],
      description: data.hint,
      category: "menudash-theme",
      icon: "store",
      supports: { html: false, align: short === "giftcard" ? ["full"] : false, inserter: ["open", "buttons", "place", "contact", "directions", "addon"].indexOf(short) < 0 }, // Now MenuDash blocks and WordPress buttons.
      edit: function (props) {
        return el("div", blockEditor.useBlockProps({ className: "mdt-live-block" }),
          el(ServerSideRender, { block: name, attributes: props.attributes }));
      },
      save: function () { return null; }
    };
    // The gift card teaser is a section of its own, full width. (Only this block gets an
    // attributes list here; the others keep theirs from PHP.)
    if (short === "giftcard") settings.attributes = { align: { type: "string", default: "full" } };
    blocks.registerBlockType(name, settings);
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.serverSideRender, window.mdtBlocks);
