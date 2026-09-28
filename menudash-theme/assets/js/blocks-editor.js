/* The theme's MenuDash blocks in the editor (inc/blocks.php): each shows a live preview
   drawn by the server from what is entered in MenuDash, and saves nothing but its name, so
   the page never keeps an old phone number or address. Names and the hint come translated
   from PHP (mdtBlocks). */
(function (blocks, element, blockEditor, ServerSideRender, data) {
  "use strict";
  var el = element.createElement;
  Object.keys(data.names).forEach(function (short) {
    var name = "menudash-theme/" + short;
    blocks.registerBlockType(name, {
      apiVersion: 3,
      title: data.names[short],
      description: data.hint,
      category: "menudash-theme",
      icon: "store",
      supports: { html: false, inserter: short !== "open" && short !== "buttons" }, // Now MenuDash's Open now block and WordPress buttons.
      edit: function (props) {
        return el("div", blockEditor.useBlockProps({ className: "mdt-live-block" }),
          el(ServerSideRender, { block: name, attributes: props.attributes }));
      },
      save: function () { return null; }
    });
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.serverSideRender, window.mdtBlocks);
