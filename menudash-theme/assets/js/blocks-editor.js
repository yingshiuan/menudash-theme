/* "Show only when …" (inc/blocks.php, mdt_show_conditions()): a setting under Advanced for
   every block. A marked block gets a dashed outline and a label in the editor, so it is
   clear why guests may not see it; the editor itself always shows it. First in this file:
   the attribute must be added before the blocks below are registered. */
(function (hooks, compose, element, blockEditor, components, data) {
  "use strict";
  if (!data || !data.show) return;
  var el = element.createElement, t = data.show;
  var options = [{ label: t.always, value: "" }].concat(Object.keys(t.when).map(function (k) { return { label: t.when[k], value: k }; }));
  function label(show) {
    return show.split(",").map(function (k) { k = k.trim(); return t.when[k] || k; }).join(" + ");
  }
  hooks.addFilter("blocks.registerBlockType", "menudash-theme/show-attribute", function (settings) {
    settings.attributes = Object.assign({}, settings.attributes, { mdtShow: { type: "string", default: "" } });
    return settings;
  });
  hooks.addFilter("editor.BlockEdit", "menudash-theme/show-setting", compose.createHigherOrderComponent(function (BlockEdit) {
    return function (props) {
      var value = props.attributes.mdtShow || "";
      // A combination the list doesn't have (from a pattern) stays selectable as it is.
      var opts = value && !t.when[value] ? options.concat([{ label: label(value), value: value }]) : options;
      return el(element.Fragment, null,
        el(BlockEdit, props),
        props.isSelected && el(blockEditor.InspectorAdvancedControls, null,
          el(components.SelectControl, {
            label: t.title, help: t.help, value: value, options: opts,
            onChange: function (v) { props.setAttributes({ mdtShow: v }); }
          })));
    };
  }, "withMdtShow"));
  hooks.addFilter("editor.BlockListBlock", "menudash-theme/show-label", compose.createHigherOrderComponent(function (BlockListBlock) {
    return function (props) {
      var show = props.attributes && props.attributes.mdtShow;
      if (!show) return el(BlockListBlock, props);
      return el(BlockListBlock, Object.assign({}, props, {
        className: [props.className, "mdt-show-when"].filter(Boolean).join(" "),
        wrapperProps: Object.assign({}, props.wrapperProps, { "data-mdt-show": t.badge + " " + label(show) })
      }));
    };
  }, "withMdtShowLabel"));
})(window.wp.hooks, window.wp.compose, window.wp.element, window.wp.blockEditor, window.wp.components, window.mdtBlocks);

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
    // The 404 number: what its 0 is, in the sidebar.
    if (short === "not-found") {
      settings.icon = "warning";
      settings.attributes = { picture: { type: "string", default: "" }, pictureId: { type: "number", default: 0 } };
      settings.edit = function (props) {
        var a = props.attributes, t = data.zero, c = window.wp.components;
        return el(element.Fragment, null,
          el(blockEditor.InspectorControls, null,
            el(c.PanelBody, { title: t.title },
              el(c.SelectControl, {
                label: t.title, value: a.picture || "", help: !a.picture ? t.autoTip : undefined,
                options: [{ label: t.auto, value: "" }, { label: t.smile, value: "smile" }, { label: t.own, value: "image" }],
                onChange: function (v) { props.setAttributes({ picture: v }); }
              }),
              a.picture === "image" && el(blockEditor.MediaUploadCheck, null,
                el(blockEditor.MediaUpload, {
                  allowedTypes: ["image"], value: a.pictureId,
                  onSelect: function (m) { props.setAttributes({ pictureId: m.id }); },
                  render: function (o) { return el(c.Button, { variant: "secondary", onClick: o.open }, a.pictureId ? t.other : t.choose); }
                })))),
          el("div", blockEditor.useBlockProps({ className: "mdt-live-block" }),
            el(ServerSideRender, { block: name, attributes: { picture: a.picture, pictureId: a.pictureId } })));
      };
    }
    blocks.registerBlockType(name, settings);
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.serverSideRender, window.mdtBlocks);

/* "Corner picture": the faint picture in the header band's right corner, chosen on the
   header's Group (class mdt-seal). The site draws it in inc/blocks.php; here the panel and a
   preview. Words come translated from PHP (mdtBlocks.mark). */
(function (hooks, compose, element, blockEditor, components, wpData, t) {
  "use strict";
  var el = element.createElement;
  var isBand = function (props) { return /(^|\s)mdt-seal(\s|$)/.test(props.attributes.className || ""); };

  hooks.addFilter("blocks.registerBlockType", "menudash-theme/mark-attributes", function (settings, name) {
    if (name !== "core/group") return settings;
    settings.attributes = Object.assign({}, settings.attributes, {
      mdtMark: { type: "string", default: "" },
      mdtMarkId: { type: "number", default: 0 },
      mdtMarkOpacity: { type: "number", default: 8 }
    });
    return settings;
  });

  hooks.addFilter("editor.BlockEdit", "menudash-theme/mark-panel", compose.createHigherOrderComponent(function (BlockEdit) {
    return function (props) {
      if (props.name !== "core/group" || !isBand(props)) return el(BlockEdit, props);
      var a = props.attributes, set = props.setAttributes;
      return el(element.Fragment, null,
        el(BlockEdit, props),
        el(blockEditor.InspectorControls, null,
          el(components.PanelBody, { title: t.title, initialOpen: true },
            el(components.SelectControl, {
              label: t.picture,
              help: t.autoTip,
              value: a.mdtMark || "",
              options: [
                { label: t.auto, value: "" },
                { label: t.ring, value: "ring" },
                { label: t.own, value: "image" },
                { label: t.none, value: "none" }
              ],
              onChange: function (v) { set({ mdtMark: v }); }
            }),
            a.mdtMark === "image" && el(blockEditor.MediaUploadCheck, null,
              el(blockEditor.MediaUpload, {
                allowedTypes: ["image"],
                value: a.mdtMarkId,
                onSelect: function (m) { set({ mdtMarkId: m.id }); },
                render: function (o) {
                  return el(components.Button, { variant: "secondary", onClick: o.open }, a.mdtMarkId ? t.other : t.choose);
                }
              })),
            a.mdtMark !== "none" && el(components.RangeControl, {
              label: t.opacity,
              help: t.opTip,
              value: a.mdtMarkOpacity,
              min: 2, max: 40,
              onChange: function (v) { set({ mdtMarkOpacity: v === undefined ? 8 : v }); },
              allowReset: true, resetFallbackValue: 8
            })
          )
        )
      );
    };
  }, "withMdtMark"));

  // Preview in the editor: the same variables and classes as on the site.
  hooks.addFilter("editor.BlockListBlock", "menudash-theme/mark-preview", compose.createHigherOrderComponent(function (BlockListBlock) {
    return function (props) {
      if (props.name !== "core/group" || !isBand(props)) return el(BlockListBlock, props);
      var a = props.attributes;
      var own = wpData.useSelect(function (select) {
        return a.mdtMark === "image" && a.mdtMarkId ? select("core").getMedia(a.mdtMarkId) : null;
      }, [a.mdtMark, a.mdtMarkId]);
      var url = a.mdtMark === "image" ? (own && own.source_url) : (!a.mdtMark ? t.icon : "");
      var cls = [props.className, a.mdtMark === "none" ? "mdt-seal-off" : "", url ? "mdt-seal-img" : "", url && !a.mdtMark ? "mdt-seal-round" : ""].filter(Boolean).join(" ");
      var style = Object.assign({}, props.wrapperProps && props.wrapperProps.style, {
        "--mdt-mark": url ? 'url("' + url + '")' : undefined,
        "--mdt-mark-opacity": (a.mdtMarkOpacity === undefined ? 8 : a.mdtMarkOpacity) / 100
      });
      return el(BlockListBlock, Object.assign({}, props, { className: cls, wrapperProps: Object.assign({}, props.wrapperProps, { style: style }) }));
    };
  }, "withMdtMarkPreview"));
})(window.wp.hooks, window.wp.compose, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.data, window.mdtBlocks.mark);
