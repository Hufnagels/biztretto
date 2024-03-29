/*!
 * jQuery++ - 1.0.1
 * http://jquerypp.com
 * Copyright (c) 2013 Bitovi
 * Wed, 17 Jul 2013 14:19:12 GMT
 * Licensed MIT
 * Download from: http://bitbuilder.herokuapp.com/jquerypp.custom.js?plugins=jquerypp%2Fdom%2Fdimensions&plugins=jquerypp%2Fdom%2Fform_params&plugins=jquerypp%2Fdom%2Fselection&plugins=jquerypp%2Fdom%2Fstyles&plugins=jquerypp%2Fdom%2Fwithin&plugins=jquerypp%2Fevent%2Fdestroyed&plugins=jquerypp%2Fevent%2Fdrag&plugins=jquerypp%2Fevent%2Fdrop&plugins=jquerypp%2Fevent%2Ffastfix&plugins=jquerypp%2Fevent%2Fresize
 */
(function($) {

    // ## jquerypp/dom/styles/styles.js
    var __m3 = (function($) {
        var getComputedStyle = document.defaultView && document.defaultView.getComputedStyle,
            // The following variables are used to convert camelcased attribute names
            // into dashed names, e.g. borderWidth to border-width
            rupper = /([A-Z])/g,
            rdashAlpha = /-([a-z])/ig,
            fcamelCase = function(all, letter) {
                return letter.toUpperCase();
            },
            // Returns the computed style for an elementn
            getStyle = function(elem) {
                if (getComputedStyle) {
                    return getComputedStyle(elem, null);
                } else if (elem.currentStyle) {
                    return elem.currentStyle;
                }
            },
            // Checks for float px and numeric values
            rfloat = /float/i,
            rnumpx = /^-?\d+(?:px)?$/i,
            rnum = /^-?\d/;

        // Returns a list of styles for a given element
        $.styles = function(el, styles) {
            if (!el) {
                return null;
            }
            var currentS = getStyle(el),
                oldName, val, style = el.style,
                results = {},
                i = 0,
                left, rsLeft, camelCase, name;

            // Go through each style
            for (; i < styles.length; i++) {
                name = styles[i];
                oldName = name.replace(rdashAlpha, fcamelCase);

                if (rfloat.test(name)) {
                    name = $.support.cssFloat ? "float" : "styleFloat";
                    oldName = "cssFloat";
                }

                // If we have getComputedStyle available
                if (getComputedStyle) {
                    // convert camelcased property names to dashed name
                    name = name.replace(rupper, "-$1").toLowerCase();
                    // use getPropertyValue of the current style object
                    val = currentS.getPropertyValue(name);
                    // default opacity is 1
                    if (name === "opacity" && val === "") {
                        val = "1";
                    }
                    results[oldName] = val;
                } else {
                    // Without getComputedStyles
                    camelCase = name.replace(rdashAlpha, fcamelCase);
                    results[oldName] = currentS[name] || currentS[camelCase];

                    // convert to px
                    if (!rnumpx.test(results[oldName]) && rnum.test(results[oldName])) {
                        // Remember the original values
                        left = style.left;
                        rsLeft = el.runtimeStyle.left;

                        // Put in the new values to get a computed value out
                        el.runtimeStyle.left = el.currentStyle.left;
                        style.left = camelCase === "fontSize" ? "1em" : (results[oldName] || 0);
                        results[oldName] = style.pixelLeft + "px";

                        // Revert the changed values
                        style.left = left;
                        el.runtimeStyle.left = rsLeft;
                    }

                }
            }

            return results;
        };


        $.fn.styles = function() {
            // Pass the arguments as an array to $.styles
            return $.styles(this[0], $.makeArray(arguments));
        };

        return $;
    })($);

    // ## jquerypp/dom/dimensions/dimensions.js
    var __m1 = (function($) {

        var
        //margin is inside border
        weird = /button|select/i,
            getBoxes = {},
            checks = {
                width: ["Left", "Right"],
                height: ['Top', 'Bottom'],
                oldOuterHeight: $.fn.outerHeight,
                oldOuterWidth: $.fn.outerWidth,
                oldInnerWidth: $.fn.innerWidth,
                oldInnerHeight: $.fn.innerHeight
            },
            supportsSetter = $.fn.jquery >= '1.8.0';

        $.each({

                width:

                "Width",

                height:

                // for each 'height' and 'width'
                "Height"
            }, function(lower, Upper) {

                //used to get the padding and border for an element in a given direction
                getBoxes[lower] = function(el, boxes) {
                    var val = 0;
                    if (!weird.test(el.nodeName)) {
                        //make what to check for ....
                        var myChecks = [];
                        $.each(checks[lower], function() {
                            var direction = this;
                            $.each(boxes, function(name, val) {
                                if (val)
                                    myChecks.push(name + direction + (name == 'border' ? "Width" : ""));
                            })
                        })
                        $.each($.styles(el, myChecks), function(name, value) {
                            val += (parseFloat(value) || 0);
                        })
                    }
                    return val;
                }

                //getter / setter
                if (!supportsSetter) {
                    $.fn["outer" + Upper] = function(v, margin) {
                        var first = this[0];
                        if (typeof v == 'number') {
                            // Setting the value
                            first && this[lower](v - getBoxes[lower](first, {
                                        padding: true,
                                        border: true,
                                        margin: margin
                                    }))
                            return this;
                        } else {
                            // Return the old value
                            return first ? checks["oldOuter" + Upper].apply(this, arguments) : null;
                        }
                    }
                    $.fn["inner" + Upper] = function(v) {
                        var first = this[0];
                        if (typeof v == 'number') {
                            // Setting the value
                            first && this[lower](v - getBoxes[lower](first, {
                                        padding: true
                                    }))
                            return this;
                        } else {
                            // Return the old value
                            return first ? checks["oldInner" + Upper].apply(this, arguments) : null;
                        }
                    }
                }

                //provides animations
                var animate = function(boxes) {
                    // Return the animation function
                    return function(fx) {
                        if (fx[supportsSetter ? 'pos' : 'state'] == 0) {
                            fx.start = $(fx.elem)[lower]();
                            fx.end = fx.end - getBoxes[lower](fx.elem, boxes);
                        }
                        fx.elem.style[lower] = (fx.pos * (fx.end - fx.start) + fx.start) + "px"
                    }
                }
                $.fx.step["outer" + Upper] = animate({
                        padding: true,
                        border: true
                    })
                $.fx.step["outer" + Upper + "Margin"] = animate({
                        padding: true,
                        border: true,
                        margin: true
                    })
                $.fx.step["inner" + Upper] = animate({
                        padding: true
                    })

            })

        return $;
    })($, __m3);

    // ## jquerypp/dom/form_params/form_params.js
    var __m4 = (function($) {
        var
        // use to parse bracket notation like my[name][attribute]
        keyBreaker = /[^\[\]]+/g,
            // converts values that look like numbers and booleans and removes empty strings
            convertValue = function(value) {
                if ($.isNumeric(value)) {
                    return parseFloat(value);
                } else if (value === 'true') {
                    return true;
                } else if (value === 'false') {
                    return false;
                } else if (value === '' || value === null) {
                    return undefined;
                }
                return value;
            },
            // Access nested data
            nestData = function(elem, type, data, parts, value, seen, fullName) {
                var name = parts.shift();
                // Keep track of the dot separated fullname. Used to uniquely track seen values
                // and if they should be converted to an array or not
                fullName = fullName ? fullName + '.' + name : name;

                if (parts.length) {
                    if (!data[name]) {
                        data[name] = {};
                    }

                    // Recursive call
                    nestData(elem, type, data[name], parts, value, seen, fullName);
                } else {

                    // Handle same name case, as well as "last checkbox checked"
                    // case
                    if (fullName in seen && type != "radio" && !$.isArray(data[name])) {
                        if (name in data) {
                            data[name] = [data[name]];
                        } else {
                            data[name] = [];
                        }
                    } else {
                        seen[fullName] = true;
                    }

                    // Finally, assign data
                    if ((type == "radio" || type == "checkbox") && !elem.is(":checked")) {
                        return
                    }

                    if (!data[name]) {
                        data[name] = value;
                    } else {
                        data[name].push(value);
                    }

                }

            };


        $.fn.extend({
                formParams: function(params) {

                    var convert;

                    // Quick way to determine if something is a boolean
                    if ( !! params === params) {
                        convert = params;
                        params = null;
                    }

                    if (params) {
                        return this.setParams(params);
                    } else {
                        return this.getParams(convert);
                    }
                },
                setParams: function(params) {

                    // Find all the inputs
                    this.find("[name]").each(function() {
                        var $this = $(this),
                            value = params[$this.attr("name")];

                        // Don't do all this work if there's no value
                        if (value !== undefined) {

                            // Nested these if statements for performance
                            if ($this.is(":radio")) {
                                if ($this.val() == value) {
                                    $this.attr("checked", true);
                                }
                            } else if ($this.is(":checkbox")) {
                                // Convert single value to an array to reduce
                                // complexity
                                value = $.isArray(value) ? value : [value];
                                if ($.inArray($this.val(), value) > -1) {
                                    $this.attr("checked", true);
                                }
                            } else {
                                $this.val(value);
                            }
                        }
                    });
                },
                getParams: function(convert) {
                    var data = {},
                        // This is used to keep track of the checkbox names that we've
                        // already seen, so we know that we should return an array if
                        // we see it multiple times. Fixes last checkbox checked bug.
                        seen = {},
                        current;

                    this.find("[name]:not(:disabled)").each(function() {
                        var $this = $(this),
                            type = $this.attr("type"),
                            name = $this.attr("name"),
                            value = $this.val(),
                            parts;

                        // Don't accumulate submit buttons and nameless elements
                        if (type == "submit" || !name) {
                            return;
                        }

                        // Figure out name parts
                        parts = name.match(keyBreaker);
                        if (!parts.length) {
                            parts = [name];
                        }

                        // Convert the value
                        if (convert) {
                            value = convertValue(value);
                        }

                        // Assign data recursively
                        nestData($this, type, data, parts, value, seen);

                    });

                    return data;
                }
            });

        return $;
    })($);

    // ## jquerypp/dom/compare/compare.js
    var __m7 = (function($) {

        // See http://ejohn.org/blog/comparing-document-position/
        $.fn.compare = function(element) { //usually
            try {
                // Firefox 3 throws an error with XUL - we can't use compare then
                element = element.jquery ? element[0] : element;
            } catch (e) {
                return null;
            }

            // make sure we aren't coming from XUL element
            if (window.HTMLElement) {
                var s = HTMLElement.prototype.toString.call(element)
                if (s == '[xpconnect wrapped native prototype]' || s == '[object XULElement]' || s === '[object Window]') {
                    return null;
                }
            }

            if (this[0].compareDocumentPosition) {
                // For browsers that support it, use compareDocumentPosition
                // https://developer.mozilla.org/en/DOM/Node.compareDocumentPosition
                return this[0].compareDocumentPosition(element);
            }

            // this[0] contains element
            if (this[0] == document && element != document) return 8;

            var number =
            // this[0] contains element
            (this[0] !== element && this[0].contains(element) && 16) +
            // element contains this[0]
            (this[0] != element && element.contains(this[0]) && 8),
                docEl = document.documentElement;

            // Use the sourceIndex
            if (this[0].sourceIndex) {
                // this[0] precedes element
                number += (this[0].sourceIndex < element.sourceIndex && 4)
                // element precedes foo[0]
                number += (this[0].sourceIndex > element.sourceIndex && 2)
                // The nodes are in different documents
                number += (this[0].ownerDocument !== element.ownerDocument ||
                    (this[0] != docEl && this[0].sourceIndex <= 0) ||
                    (element != docEl && element.sourceIndex <= 0)) && 1
            }

            return number;
        }

        return $;
    })($);

    // ## jquerypp/dom/range/range.js
    var __m6 = (function($) {


        $.fn.range = function() {
            return $.Range(this[0])
        }

        var convertType = function(type) {
            return type.replace(/([a-z])([a-z]+)/gi, function(all, first, next) {
                return first + next.toLowerCase()
            }).replace(/_/g, "");
        },
            // reverses things like START_TO_END into END_TO_START
            reverse = function(type) {
                return type.replace(/^([a-z]+)_TO_([a-z]+)/i, function(all, first, last) {
                    return last + "_TO_" + first;
                });
            },
            getWindow = function(element) {
                return element ? element.ownerDocument.defaultView || element.ownerDocument.parentWindow : window
            },
            bisect = function(el, start, end) {
                //split the start and end ... figure out who is touching ...
                if (end - start == 1) {
                    return
                }
            },
            support = {};

        $.Range = function(range) {
            // If it's called w/o new, call it with new!
            if (this.constructor !== $.Range) {
                return new $.Range(range);
            }
            // If we are passed a jQuery-wrapped element, get the raw element
            if (range && range.jquery) {
                range = range[0];
            }
            // If we have an element, or nothing
            if (!range || range.nodeType) {
                // create a range
                this.win = getWindow(range)
                if (this.win.document.createRange) {
                    this.range = this.win.document.createRange()
                } else if (this.win && this.win.document.body && this.win.document.body.createTextRange) {
                    this.range = this.win.document.body.createTextRange()
                }
                // if we have an element, make the range select it
                if (range) {
                    this.select(range)
                }
            }
            // if we are given a point
            else if (range.clientX != null || range.pageX != null || range.left != null) {
                this.moveToPoint(range);
            }
            // if we are given a touch event
            else if (range.originalEvent && range.originalEvent.touches && range.originalEvent.touches.length) {
                this.moveToPoint(range.originalEvent.touches[0])

            }
            // if we are a normal event
            else if (range.originalEvent && range.originalEvent.changedTouches && range.originalEvent.changedTouches.length) {
                this.moveToPoint(range.originalEvent.changedTouches[0])
            }
            // given a TextRange or something else?
            else {
                this.range = range;
            }
        };

        $.Range.

        current = function(el) {
            var win = getWindow(el),
                selection;
            if (win.getSelection) {
                // If we can get the selection
                selection = win.getSelection()
                return new $.Range(selection.rangeCount ? selection.getRangeAt(0) : win.document.createRange())
            } else {
                // Otherwise use document.selection
                return new $.Range(win.document.selection.createRange());
            }
        };

        $.extend($.Range.prototype,

            {

                moveToPoint: function(point) {
                    var clientX = point.clientX,
                        clientY = point.clientY
                    if (!clientX) {
                        var off = scrollOffset();
                        clientX = (point.pageX || point.left || 0) - off.left;
                        clientY = (point.pageY || point.top || 0) - off.top;
                    }
                    if (support.moveToPoint) {
                        this.range = $.Range().range
                        this.range.moveToPoint(clientX, clientY);
                        return this;
                    }

                    // it's some text node in this range ...
                    var parent = document.elementFromPoint(clientX, clientY);

                    //typically it will be 'on' text
                    for (var n = 0; n < parent.childNodes.length; n++) {
                        var node = parent.childNodes[n];
                        if (node.nodeType === 3 || node.nodeType === 4) {
                            var range = $.Range(node),
                                length = range.toString().length;

                            // now lets start moving the end until the boundingRect is within our range
                            for (var i = 1; i < length + 1; i++) {
                                var rect = range.end(i).rect();
                                if (rect.left <= clientX && rect.left + rect.width >= clientX &&
                                    rect.top <= clientY && rect.top + rect.height >= clientY) {
                                    range.start(i - 1);
                                    this.range = range.range;
                                    return this;
                                }
                            }
                        }
                    }

                    // if not 'on' text, recursively go through and find out when we shift to next
                    // 'line'
                    var previous;
                    iterate(parent.childNodes, function(textNode) {
                        var range = $.Range(textNode);
                        if (range.rect().top > point.clientY) {
                            return false;
                        } else {
                            previous = range;
                        }
                    });

                    if (previous) {
                        previous.start(previous.toString().length);
                        this.range = previous.range;
                    } else {
                        this.range = $.Range(parent).range
                    }
                },

                window: function() {
                    return this.win || window;
                },

                overlaps: function(elRange) {
                    if (elRange.nodeType) {
                        elRange = $.Range(elRange).select(elRange);
                    }
                    //if the start is within the element ...
                    var startToStart = this.compare("START_TO_START", elRange),
                        endToEnd = this.compare("END_TO_END", elRange)

                        // if we wrap elRange
                        if (startToStart <= 0 && endToEnd >= 0) {
                            return true;
                        }
                        // if our start is inside of it
                    if (startToStart >= 0 &&
                        this.compare("START_TO_END", elRange) <= 0) {
                        return true;
                    }
                    // if our end is inside of elRange
                    if (this.compare("END_TO_START", elRange) >= 0 &&
                        endToEnd <= 0) {
                        return true;
                    }
                    return false;
                },

                collapse: function(toStart) {
                    this.range.collapse(toStart === undefined ? true : toStart);
                    return this;
                },

                toString: function() {
                    return typeof this.range.text == "string" ? this.range.text : this.range.toString();
                },

                start: function(set) {
                    // return start
                    if (set === undefined) {
                        if (this.range.startContainer) {
                            return {
                                container: this.range.startContainer,
                                offset: this.range.startOffset
                            }
                        } else {
                            // Get the start parent element
                            var start = this.clone().collapse().parent();
                            // used to get the start element offset
                            var startRange = $.Range(start).select(start).collapse();
                            startRange.move("END_TO_START", this);
                            return {
                                container: start,
                                offset: startRange.toString().length
                            }
                        }
                    } else {
                        if (this.range.setStart) {
                            // supports setStart
                            if (typeof set == 'number') {
                                this.range.setStart(this.range.startContainer, set)
                            } else if (typeof set == 'string') {
                                var res = callMove(this.range.startContainer, this.range.startOffset, parseInt(set, 10))
                                this.range.setStart(res.node, res.offset);
                            } else {
                                this.range.setStart(set.container, set.offset)
                            }
                        } else {
                            if (typeof set == "string") {
                                this.range.moveStart('character', parseInt(set, 10))
                            } else {
                                // get the current end container
                                var container = this.start().container,
                                    offset
                                if (typeof set == "number") {
                                    offset = set
                                } else {
                                    container = set.container
                                    offset = set.offset
                                }
                                var newPoint = $.Range(container).collapse();
                                //move it over offset characters
                                newPoint.range.move(offset);
                                this.move("START_TO_START", newPoint);
                            }
                        }
                        return this;
                    }

                },

                end: function(set) {
                    // read end
                    if (set === undefined) {
                        if (this.range.startContainer) {
                            return {
                                container: this.range.endContainer,
                                offset: this.range.endOffset
                            }
                        } else {
                            var
                            // Get the end parent element
                            end = this.clone().collapse(false).parent(),
                                // used to get the end elements offset
                                endRange = $.Range(end).select(end).collapse();
                            endRange.move("END_TO_END", this);
                            return {
                                container: end,
                                offset: endRange.toString().length
                            }
                        }
                    } else {
                        if (this.range.setEnd) {
                            if (typeof set == 'number') {
                                this.range.setEnd(this.range.endContainer, set)
                            } else if (typeof set == 'string') {
                                var res = callMove(this.range.endContainer, this.range.endOffset, parseInt(set, 10))
                                this.range.setEnd(res.node, res.offset);
                            } else {
                                this.range.setEnd(set.container, set.offset)
                            }
                        } else {
                            if (typeof set == "string") {
                                this.range.moveEnd('character', parseInt(set, 10));
                            } else {
                                // get the current end container
                                var container = this.end().container,
                                    offset
                                if (typeof set == "number") {
                                    offset = set
                                } else {
                                    container = set.container
                                    offset = set.offset
                                }
                                var newPoint = $.Range(container).collapse();
                                //move it over offset characters
                                newPoint.range.move(offset);
                                this.move("END_TO_START", newPoint);
                            }
                        }
                        return this;
                    }
                },

                parent: function() {
                    if (this.range.commonAncestorContainer) {
                        return this.range.commonAncestorContainer;
                    } else {

                        var parentElement = this.range.parentElement(),
                            range = this.range;

                        // IE's parentElement will always give an element, we want text ranges
                        iterate(parentElement.childNodes, function(txtNode) {
                            if ($.Range(txtNode).range.inRange(range)) {
                                // swap out the parentElement
                                parentElement = txtNode;
                                return false;
                            }
                        });

                        return parentElement;
                    }
                },

                rect: function(from) {
                    var rect = this.range.getBoundingClientRect();
                    // for some reason in webkit this gets a better value
                    if (!rect.height && !rect.width) {
                        rect = this.range.getClientRects()[0]
                    }
                    if (from === 'page') {
                        // Add the scroll offset
                        var off = scrollOffset();
                        rect = $.extend({}, rect);
                        rect.top += off.top;
                        rect.left += off.left;
                    }
                    return rect;
                },

                rects: function(from) {
                    // order rects by size
                    var rects = $.map($.makeArray(this.range.getClientRects()).sort(function(rect1, rect2) {
                                return rect2.width * rect2.height - rect1.width * rect1.height;
                            }), function(rect) {
                            return $.extend({}, rect)
                        }),
                        i = 0,
                        j,
                        len = rects.length;

                    // safari returns overlapping client rects
                    //     - big rects can contain 2 smaller rects
                    //     - some rects can contain 0 - width rects
                    //     - we don't want these 0 width rects
                    while (i < rects.length) {
                        var cur = rects[i],
                            found = false;

                        j = i + 1;
                        while (j < rects.length) {
                            if (withinRect(cur, rects[j])) {
                                if (!rects[j].width) {
                                    rects.splice(j, 1)
                                } else {
                                    found = rects[j];
                                    break;
                                }
                            } else {
                                j++;
                            }
                        }

                        if (found) {
                            rects.splice(i, 1)
                        } else {
                            i++;
                        }

                    }
                    // safari will be return overlapping ranges ...
                    if (from == 'page') {
                        var off = scrollOffset();
                        return $.each(rects, function(ith, item) {
                            item.top += off.top;
                            item.left += off.left;
                        })
                    }

                    return rects;
                }

            });
        (function() {
            //method branching ....
            var fn = $.Range.prototype,
                range = $.Range().range;


            fn.compare = range.compareBoundaryPoints ? function(type, range) {
                return this.range.compareBoundaryPoints(this.window().Range[reverse(type)], range.range)
            } : function(type, range) {
                return this.range.compareEndPoints(convertType(type), range.range)
            }


            fn.move = range.setStart ? function(type, range) {

                var rangesRange = range.range;
                switch (type) {
                    case "START_TO_END":
                        this.range.setStart(rangesRange.endContainer, rangesRange.endOffset)
                        break;
                    case "START_TO_START":
                        this.range.setStart(rangesRange.startContainer, rangesRange.startOffset)
                        break;
                    case "END_TO_END":
                        this.range.setEnd(rangesRange.endContainer, rangesRange.endOffset)
                        break;
                    case "END_TO_START":
                        this.range.setEnd(rangesRange.startContainer, rangesRange.startOffset)
                        break;
                }

                return this;
            } : function(type, range) {
                this.range.setEndPoint(convertType(type), range.range)
                return this;
            };
            var cloneFunc = range.cloneRange ? "cloneRange" : "duplicate",
                selectFunc = range.selectNodeContents ? "selectNodeContents" : "moveToElementText";

            fn.

            clone = function() {
                return $.Range(this.range[cloneFunc]());
            };

            fn.

            select = range.selectNodeContents ? function(el) {
                if (!el) {
                    var selection = this.window().getSelection();
                    selection.removeAllRanges();
                    selection.addRange(this.range);
                } else {
                    this.range.selectNodeContents(el);
                }
                return this;
            } : function(el) {
                if (!el) {
                    this.range.select()
                } else if (el.nodeType === 3) {
                    //select this node in the element ...
                    var parent = el.parentNode,
                        start = 0,
                        end;
                    iterate(parent.childNodes, function(txtNode) {
                        if (txtNode === el) {
                            end = start + txtNode.nodeValue.length;
                            return false;
                        } else {
                            start = start + txtNode.nodeValue.length
                        }
                    })
                    this.range.moveToElementText(parent);

                    this.range.moveEnd('character', end - this.range.text.length)
                    this.range.moveStart('character', start);
                } else {
                    this.range.moveToElementText(el);
                }
                return this;
            };

        })();

        // helpers  -----------------

        // iterates through a list of elements, calls cb on every text node
        // if cb returns false, exits the iteration
        var iterate = function(elems, cb) {
            var elem, start;
            for (var i = 0; elems[i]; i++) {
                elem = elems[i];
                // Get the text from text nodes and CDATA nodes
                if (elem.nodeType === 3 || elem.nodeType === 4) {
                    if (cb(elem) === false) {
                        return false;
                    }
                    // Traverse everything else, except comment nodes
                } else if (elem.nodeType !== 8) {
                    if (iterate(elem.childNodes, cb) === false) {
                        return false;
                    }
                }
            }

        },
            isText = function(node) {
                return node.nodeType === 3 || node.nodeType === 4
            },
            iteratorMaker = function(toChildren, toNext) {
                return function(node, mustMoveRight) {
                    // first try down
                    if (node[toChildren] && !mustMoveRight) {
                        return isText(node[toChildren]) ?
                            node[toChildren] :
                            arguments.callee(node[toChildren])
                    } else if (node[toNext]) {
                        return isText(node[toNext]) ?
                            node[toNext] :
                            arguments.callee(node[toNext])
                    } else if (node.parentNode) {
                        return arguments.callee(node.parentNode, true)
                    }
                }
            },
            getNextTextNode = iteratorMaker("firstChild", "nextSibling"),
            getPrevTextNode = iteratorMaker("lastChild", "previousSibling"),
            callMove = function(container, offset, howMany) {
                var mover = howMany < 0 ?
                    getPrevTextNode : getNextTextNode;

                // find the text element
                if (!isText(container)) {
                    // sometimes offset isn't actually an element
                    container = container.childNodes[offset] ?
                        container.childNodes[offset] :
                    // if this happens, use the last child
                    container.lastChild;

                    if (!isText(container)) {
                        container = mover(container)
                    }
                    return move(container, howMany)
                } else {
                    if (offset + howMany < 0) {
                        return move(mover(container), offset + howMany)
                    } else {
                        return move(container, offset + howMany)
                    }

                }
            },
            // Moves howMany characters from the start of
            // from
            move = function(from, howMany) {
                var mover = howMany < 0 ?
                    getPrevTextNode : getNextTextNode;

                howMany = Math.abs(howMany);

                while (from && howMany >= from.nodeValue.length) {
                    howMany = howMany - from.nodeValue.length;
                    from = mover(from)
                }
                return {
                    node: from,
                    offset: mover === getNextTextNode ? howMany : from.nodeValue.length - howMany
                }
            },
            supportWhitespace,
            isWhitespace = function(el) {
                if (supportWhitespace == null) {
                    supportWhitespace = 'isElementContentWhitespace' in el;
                }
                return (supportWhitespace ? el.isElementContentWhitespace :
                    (el.nodeType === 3 && '' == el.data.trim()));

            },
            // if a point is within a rectangle
            within = function(rect, point) {

                return rect.left <= point.clientX && rect.left + rect.width >= point.clientX &&
                    rect.top <= point.clientY &&
                    rect.top + rect.height >= point.clientY
            },
            // if a rectangle is within another rectangle
            withinRect = function(outer, inner) {
                return within(outer, {
                        clientX: inner.left,
                        clientY: inner.top
                    }) && //top left
                within(outer, {
                        clientX: inner.left + inner.width,
                        clientY: inner.top
                    }) && //top right
                within(outer, {
                        clientX: inner.left,
                        clientY: inner.top + inner.height
                    }) && //bottom left
                within(outer, {
                        clientX: inner.left + inner.width,
                        clientY: inner.top + inner.height
                    }) //bottom right
            },
            // gets the scroll offset from a window
            scrollOffset = function(win) {
                var win = win || window;
                doc = win.document.documentElement, body = win.document.body;

                return {
                    left: (doc && doc.scrollLeft || body && body.scrollLeft || 0) + (doc.clientLeft || 0),
                    top: (doc && doc.scrollTop || body && body.scrollTop || 0) + (doc.clientTop || 0)
                };
            };

        support.moveToPoint = !! $.Range().range.moveToPoint

        return $;
    })($, __m7);

    // ## jquerypp/dom/selection/selection.js
    var __m5 = (function($) {

        var getWindow = function(element) {
            return element ? element.ownerDocument.defaultView || element.ownerDocument.parentWindow : window
        },
            // A helper that uses range to abstract out getting the current start and endPos.
            getElementsSelection = function(el, win) {
                // get a copy of the current range and a range that spans the element
                var current = $.Range.current(el).clone(),
                    entireElement = $.Range(el).select(el);
                // if there is no overlap, there is nothing selected
                if (!current.overlaps(entireElement)) {
                    return null;
                }
                // if the current range starts before our element
                if (current.compare("START_TO_START", entireElement) < 1) {
                    // the selection within the element begins at 0
                    startPos = 0;
                    // move the current range to start at our element
                    current.move("START_TO_START", entireElement);
                } else {
                    // Make a copy of the element's range.
                    // Move it's end to the start of the selected range
                    // The length of the copy is the start of the selected
                    // range.
                    fromElementToCurrent = entireElement.clone();
                    fromElementToCurrent.move("END_TO_START", current);
                    startPos = fromElementToCurrent.toString().length
                }

                // If the current range ends after our element
                if (current.compare("END_TO_END", entireElement) >= 0) {
                    // the end position is the last character
                    endPos = entireElement.toString().length
                } else {
                    // otherwise, it's the start position plus the current range
                    // TODO: this doesn't seem like it works if current
                    // extends to the left of the element.
                    endPos = startPos + current.toString().length
                }
                return {
                    start: startPos,
                    end: endPos,
                    width: endPos - startPos
                };
            },
            // Text selection works differently for selection in an input vs
            // normal html elements like divs, spans, and ps.
            // This function branches between the various methods of getting the selection.
            getSelection = function(el) {
                var win = getWindow(el);

                // `selectionStart` means this is an input element in a standards browser.
                if (el.selectionStart !== undefined) {

                    if (document.activeElement && document.activeElement != el && el.selectionStart == el.selectionEnd && el.selectionStart == 0) {
                        return {
                            start: el.value.length,
                            end: el.value.length,
                            width: 0
                        };
                    }
                    return {
                        start: el.selectionStart,
                        end: el.selectionEnd,
                        width: el.selectionEnd - el.selectionStart
                    };
                }
                // getSelection means a 'normal' element in a standards browser.
                else if (win.getSelection) {
                    return getElementsSelection(el, win)
                } else {
                    // IE will freak out, where there is no way to detect it, so we provide a callback if it does.
                    try {
                        // The following typically works for input elements in IE:
                        if (el.nodeName.toLowerCase() == 'input') {
                            var real = getWindow(el).document.selection.createRange(),
                                r = el.createTextRange();
                            r.setEndPoint("EndToStart", real);

                            var start = r.text.length
                            return {
                                start: start,
                                end: start + real.text.length,
                                width: real.text.length
                            }
                        }
                        // This works on textareas and other elements
                        else {
                            var res = getElementsSelection(el, win)
                            if (!res) {
                                return res;
                            }
                            // we have to clean up for ie's textareas which don't count for
                            // newlines correctly
                            var current = $.Range.current().clone(),
                                r2 = current.clone().collapse().range,
                                r3 = current.clone().collapse(false).range;

                            r2.moveStart('character', -1)
                            r3.moveStart('character', -1)
                            // if we aren't at the start, but previous is empty, we are at start of newline
                            if (res.startPos != 0 && r2.text == "") {
                                res.startPos += 2;
                            }
                            // do a similar thing for the end of the textarea
                            if (res.endPos != 0 && r3.text == "") {
                                res.endPos += 2;
                            }

                            return res
                        }
                    } catch (e) {
                        return {
                            start: el.value.length,
                            end: el.value.length,
                            width: 0
                        };
                    }
                }
            },
            // Selects text within an element.  Depending if it's a form element or
            // not, or a standards based browser or not, we do different things.
            select = function(el, start, end) {
                var win = getWindow(el);
                // IE behaves bad even if it sorta supports
                // getSelection so we have to try the IE methods first. barf.
                if (el.setSelectionRange) {
                    if (end === undefined) {
                        el.focus();
                        el.setSelectionRange(start, start);
                    } else {
                        el.select();
                        el.selectionStart = start;
                        el.selectionEnd = end;
                    }
                } else if (el.createTextRange) {
                    var r = el.createTextRange();
                    r.moveStart('character', start);
                    end = end || start;
                    r.moveEnd('character', end - el.value.length);

                    r.select();
                } else if (win.getSelection) {
                    var doc = win.document,
                        sel = win.getSelection(),
                        range = doc.createRange(),
                        ranges = [start, end !== undefined ? end : start];
                    getCharElement([el], ranges);
                    range.setStart(ranges[0].el, ranges[0].count);
                    range.setEnd(ranges[1].el, ranges[1].count);

                    // removeAllRanges is necessary for webkit
                    sel.removeAllRanges();
                    sel.addRange(range);

                } else if (win.document.body.createTextRange) { //IE's weirdness
                    var range = document.body.createTextRange();
                    range.moveToElementText(el);
                    range.collapse()
                    range.moveStart('character', start)
                    range.moveEnd('character', end !== undefined ? end : start)
                    range.select();
                }

            },
            // If one of the range values is within start and len, replace the range
            // value with the element and its offset.
            replaceWithLess = function(start, len, range, el) {
                if (typeof range[0] === 'number' && range[0] < len) {
                    range[0] = {
                        el: el,
                        count: range[0] - start
                    };
                }
                if (typeof range[1] === 'number' && range[1] <= len) {
                    range[1] = {
                        el: el,
                        count: range[1] - start
                    }
                }
            },
            getCharElement = function(elems, range, len) {
                var elem,
                    start;

                len = len || 0;
                for (var i = 0; elems[i]; i++) {
                    elem = elems[i];
                    // Get the text from text nodes and CDATA nodes
                    if (elem.nodeType === 3 || elem.nodeType === 4) {
                        start = len
                        len += elem.nodeValue.length;
                        //check if len is now greater than what's in counts
                        replaceWithLess(start, len, range, elem)
                        // Traverse everything else, except comment nodes
                    } else if (elem.nodeType !== 8) {
                        len = getCharElement(elem.childNodes, range, len);
                    }
                }
                return len;
            };

        $.fn.selection = function(start, end) {
            if (start !== undefined) {
                return this.each(function() {
                    select(this, start, end)
                })
            } else {
                return getSelection(this[0])
            }
        };
        // for testing
        $.fn.selection.getCharElement = getCharElement;

        return $;
    })($, __m6);

    // ## jquerypp/dom/within/within.js
    var __m8 = (function($) {
        // Checks if x and y coordinates are within a box with left, top, width and height
        var withinBox = function(x, y, left, top, width, height) {
            return (y >= top &&
                y < top + height &&
                x >= left &&
                x < left + width);
        }

        $.fn.within = function(left, top, useOffsetCache) {
            var ret = []
            this.each(function() {
                var q = jQuery(this);

                if (this == document.documentElement) {
                    return ret.push(this);
                }

                // uses either the cached offset or .offset()
                var offset = useOffsetCache ?
                    $.data(this, "offsetCache") || $.data(this, "offsetCache", q.offset()) :
                    q.offset();

                // Check if the given coordinates are within the area of the current element
                var res = withinBox(left, top, offset.left, offset.top,
                    this.offsetWidth, this.offsetHeight);

                if (res) {
                    // Add it to the results
                    ret.push(this);
                }
            });

            return this.pushStack($.unique(ret), "within", left + "," + top);
        }

        $.fn.withinBox = function(left, top, width, height, useOffsetCache) {
            var ret = []
            this.each(function() {
                var q = jQuery(this);

                if (this == document.documentElement) return ret.push(this);

                // use cached offset or .offset()
                var offset = useOffsetCache ?
                    $.data(this, "offset") ||
                    $.data(this, "offset", q.offset()) :
                    q.offset();

                var ew = q.width(),
                    eh = q.height(),
                    // Checks if the element offset is within the given box
                    res = !((offset.top > top + height) || (offset.top + eh < top) || (offset.left > left + width) || (offset.left + ew < left));

                if (res)
                    ret.push(this);
            });
            return this.pushStack($.unique(ret), "withinBox", $.makeArray(arguments).join(","));
        }

        return $;
    })($);

    // ## jquerypp/event/destroyed/destroyed.js
    var __m9 = (function($) {


        // Store the old jQuery.cleanData
        var oldClean = $.cleanData;

        // Overwrites cleanData which is called by jQuery on manipulation methods
        $.cleanData = function(elems) {
            for (var i = 0, elem;
                (elem = elems[i]) !== undefined; i++) {
                // Trigger the destroyed event
                $(elem).triggerHandler("destroyed");
            }
            // Call the old jQuery.cleanData
            oldClean(elems);
        };
        return $;
    })($);

    // ## jquerypp/lang/vector/vector.js
    var __m12 = (function($) {
        var getSetZero = function(v) {
            return v !== undefined ? (this.array[0] = v) : this.array[0]
        },
            getSetOne = function(v) {
                return v !== undefined ? (this.array[1] = v) : this.array[1]
            };

        $.Vector = function(arr) {
            var array = $.isArray(arr) ? arr : $.makeArray(arguments);
            this.update(array);
        };
        $.Vector.prototype =

        {

            app: function(f) {
                var i, newArr = [];

                for (i = 0; i < this.array.length; i++) {
                    newArr.push(f(this.array[i], i));
                }
                return new $.Vector(newArr);
            },

            plus: function() {
                var i, args = arguments[0] instanceof $.Vector ? arguments[0].array : $.makeArray(arguments),
                    arr = this.array.slice(0),
                    vec = new $.Vector();
                for (i = 0; i < args.length; i++) {
                    arr[i] = (arr[i] ? arr[i] : 0) + args[i];
                }
                return vec.update(arr);
            },

            minus: function() {
                var i, args = arguments[0] instanceof $.Vector ? arguments[0].array : $.makeArray(arguments),
                    arr = this.array.slice(0),
                    vec = new $.Vector();
                for (i = 0; i < args.length; i++) {
                    arr[i] = (arr[i] ? arr[i] : 0) - args[i];
                }
                return vec.update(arr);
            },

            equals: function() {
                var i, args = arguments[0] instanceof $.Vector ? arguments[0].array : $.makeArray(arguments),
                    arr = this.array.slice(0),
                    vec = new $.Vector();
                for (i = 0; i < args.length; i++) {
                    if (arr[i] != args[i]) {
                        return null;
                    }
                }
                return vec.update(arr);
            },

            x: getSetZero,

            left: getSetZero,

            width: getSetZero,

            y: getSetOne,

            top: getSetOne,

            height: getSetOne,

            toString: function() {
                return "(" + this.array.join(', ') + ")";
            },

            update: function(array) {
                var i;
                if (this.array) {
                    for (i = 0; i < this.array.length; i++) {
                        delete this.array[i];
                    }
                }
                this.array = array;
                for (i = 0; i < array.length; i++) {
                    this[i] = this.array[i];
                }
                return this;
            }
        };

        $.Event.prototype.vector = function() {
            // Get the first touch element for touch events
            var touches = "ontouchend" in document && this.originalEvent.touches && this.originalEvent.touches.length ? this.originalEvent.changedTouches[0] : this;
            if (this.originalEvent.synthetic) {
                var doc = document.documentElement,
                    body = document.body;
                return new $.Vector(touches.clientX + (doc && doc.scrollLeft || body && body.scrollLeft || 0) - (doc.clientLeft || 0),
                    touches.clientY + (doc && doc.scrollTop || body && body.scrollTop || 0) - (doc.clientTop || 0));
            } else {
                return new $.Vector(touches.pageX, touches.pageY);
            }
        };

        $.fn.offsetv = function() {
            if (this[0] == window) {
                return new $.Vector(window.pageXOffset ? window.pageXOffset : document.documentElement.scrollLeft, window.pageYOffset ? window.pageYOffset : document.documentElement.scrollTop);
            } else {
                var offset = this.offset() || {};
                return new $.Vector(offset.left, offset.top);
            }
        };

        $.fn.dimensionsv = function(which) {
            if (this[0] == window || !which) {
                return new $.Vector(this.width(), this.height());
            } else {
                return new $.Vector(this[which + "Width"](), this[which + "Height"]());
            }
        };

        return $;
    })($);

    // ## jquerypp/event/livehack/livehack.js
    var __m13 = (function($) {

        var event = $.event,

            //helper that finds handlers by type and calls back a function, this is basically handle
            // events - the events object
            // types - an array of event types to look for
            // callback(type, handlerFunc, selector) - a callback
            // selector - an optional selector to filter with, if there, matches by selector
            //     if null, matches anything, otherwise, matches with no selector
            findHelper = function(events, types, callback, selector) {
                var t, type, typeHandlers, all, h, handle,
                    namespaces, namespace,
                    match;
                for (t = 0; t < types.length; t++) {
                    type = types[t];
                    all = type.indexOf(".") < 0;
                    if (!all) {
                        namespaces = type.split(".");
                        type = namespaces.shift();
                        namespace = new RegExp("(^|\\.)" + namespaces.slice(0).sort().join("\\.(?:.*\\.)?") + "(\\.|$)");
                    }
                    typeHandlers = (events[type] || []).slice(0);

                    for (h = 0; h < typeHandlers.length; h++) {
                        handle = typeHandlers[h];

                        match = (all || namespace.test(handle.namespace));

                        if (match) {
                            if (selector) {
                                if (handle.selector === selector) {
                                    callback(type, handle.origHandler || handle.handler);
                                }
                            } else if (selector === null) {
                                callback(type, handle.origHandler || handle.handler, handle.selector);
                            } else if (!handle.selector) {
                                callback(type, handle.origHandler || handle.handler);

                            }
                        }


                    }
                }
            };


        event.find = function(el, types, selector) {
            var events = ($._data(el) || {}).events,
                handlers = [],
                t, liver, live;

            if (!events) {
                return handlers;
            }
            findHelper(events, types, function(type, handler) {
                handlers.push(handler);
            }, selector);
            return handlers;
        };

        event.findBySelector = function(el, types) {
            var events = $._data(el).events,
                selectors = {},
                //adds a handler for a given selector and event
                add = function(selector, event, handler) {
                    var select = selectors[selector] || (selectors[selector] = {}),
                        events = select[event] || (select[event] = []);
                    events.push(handler);
                };

            if (!events) {
                return selectors;
            }
            //first check live:

            //then check straight binds
            findHelper(events, types, function(type, handler, selector) {
                add(selector || "", type, handler);
            }, null);

            return selectors;
        };
        event.supportTouch = "ontouchend" in document;

        $.fn.respondsTo = function(events) {
            if (!this.length) {
                return false;
            } else {
                //add default ?
                return event.find(this[0], $.isArray(events) ? events : [events]).length > 0;
            }
        };
        $.fn.triggerHandled = function(event, data) {
            event = (typeof event == "string" ? $.Event(event) : event);
            this.trigger(event, data);
            return event.handled;
        };

        event.setupHelper = function(types, startingEvent, onFirst) {
            if (!onFirst) {
                onFirst = startingEvent;
                startingEvent = null;
            }
            var add = function(handleObj) {
                var bySelector,
                    selector = handleObj.selector || "",
                    namespace = handleObj.namespace ? '.' + handleObj.namespace : '';

                if (selector) {
                    bySelector = event.find(this, types, selector);
                    if (!bySelector.length) {
                        $(this).delegate(selector, startingEvent + namespace, onFirst);
                    }
                } else {
                    //var bySelector = event.find(this, types, selector);
                    if (!event.find(this, types, selector).length) {
                        event.add(this, startingEvent + namespace, onFirst, {
                                selector: selector,
                                delegate: this
                            });
                    }

                }

            },
                remove = function(handleObj) {
                    var bySelector, selector = handleObj.selector || "";
                    if (selector) {
                        bySelector = event.find(this, types, selector);
                        if (!bySelector.length) {
                            $(this).undelegate(selector, startingEvent, onFirst);
                        }
                    } else {
                        if (!event.find(this, types, selector).length) {
                            event.remove(this, startingEvent, onFirst, {
                                    selector: selector,
                                    delegate: this
                                });
                        }
                    }
                };
            $.each(types, function() {
                event.special[this] = {
                    add: add,
                    remove: remove,
                    setup: function() {},
                    teardown: function() {}
                };
            });
        };

        return $;
    })($);

    // ## jquerypp/event/reverse/reverse.js
    var __m14 = (function($) {
        $.event.reverse = function(name, attributes) {
            var bound = $(),
                count = 0,
                dispatch = $.event.handle || $.event.dispatch;

            $.event.special[name] = {
                setup: function() {
                    // add and sort the resizers array
                    // don't add window because it can't be compared easily
                    if (this !== window) {
                        bound.push(this);
                        $.unique(bound);
                    }
                    // returns false if the window
                    return this !== window;
                },
                teardown: function() {
                    // we shouldn't have to sort
                    bound = bound.not(this);
                    // returns false if the window
                    return this !== window;
                },
                add: function(handleObj) {
                    var origHandler = handleObj.handler;
                    handleObj.origHandler = origHandler;

                    handleObj.handler = function(ev, data) {
                        var isWindow = this === window;
                        if (attributes && attributes.handler) {
                            var result = attributes.handler.apply(this, arguments);
                            if (result === true) {
                                return;
                            }
                        }

                        // if this is the first handler for this event ...
                        if (count === 0) {
                            // prevent others from doing what we are about to do
                            count++;
                            var where = data === false ? ev.target : this

                            // trigger all this element's handlers
                            dispatch.call(where, ev, data);
                            if (ev.isPropagationStopped()) {
                                count--;
                                return;
                            }

                            // get all other elements within this element that listen to move
                            // and trigger their resize events
                            var index = bound.index(this),
                                length = bound.length,
                                child, sub;

                            // if index == -1 it's the window
                            while (++index < length && (child = bound[index]) && (isWindow || $.contains(where, child))) {

                                // call the event
                                dispatch.call(child, ev, data);

                                if (ev.isPropagationStopped()) {
                                    // move index until the item is not in the current child
                                    while (++index < length && (sub = bound[index])) {
                                        if (!$.contains(child, sub)) {
                                            // set index back one
                                            index--;
                                            break
                                        }
                                    }
                                }
                            }

                            // prevent others from responding
                            ev.stopImmediatePropagation();
                            count--;
                        } else {
                            handleObj.origHandler.call(this, ev, data);
                        }
                    }
                }
            };

            // automatically bind on these
            $([document, window]).bind(name, function() {});

            return $.event.special[name];
        }

        return $;
    })($);

    // ## jquerypp/event/drag/core/core.js
    var __m11 = (function($) {

        if (!$.event.special.move) {
            $.event.reverse('move');
        }

        //modify live
        //steal the live handler ....
        var bind = function(object, method) {
            var args = Array.prototype.slice.call(arguments, 2);
            return function() {
                var args2 = [this].concat(args, $.makeArray(arguments));
                return method.apply(object, args2);
            };
        },
            event = $.event,
            // function to clear the window selection if there is one
            clearSelection = window.getSelection ? function() {
                window.getSelection().removeAllRanges()
            } : function() {},

            supportTouch = "ontouchend" in document,
            // Use touch events or map it to mouse events
            startEvent = supportTouch ? "touchstart" : "mousedown",
            stopEvent = supportTouch ? "touchend" : "mouseup",
            moveEvent = supportTouch ? "touchmove" : "mousemove",
            // On touchmove events the default (scrolling) event has to be prevented
            preventTouchScroll = function(ev) {
                ev.preventDefault();
            };


        $.Drag = function() {};


        $.extend($.Drag, {
                lowerName: "drag",
                current: null,
                distance: 0,

                mousedown: function(ev, element) {
                    var isLeftButton = ev.button === 0 || ev.button == 1,
                        doEvent = isLeftButton || supportTouch;

                    if (!doEvent || this.current) {
                        return;
                    }

                    //create Drag
                    var drag = new $.Drag(),
                        delegate = ev.delegateTarget || element,
                        selector = ev.handleObj.selector,
                        self = this;
                    this.current = drag;

                    drag.setup({
                            element: element,
                            delegate: ev.delegateTarget || element,
                            selector: ev.handleObj.selector,
                            moved: false,
                            _distance: this.distance,
                            callbacks: {
                                dragdown: event.find(delegate, ["dragdown"], selector),
                                draginit: event.find(delegate, ["draginit"], selector),
                                dragover: event.find(delegate, ["dragover"], selector),
                                dragmove: event.find(delegate, ["dragmove"], selector),
                                dragout: event.find(delegate, ["dragout"], selector),
                                dragend: event.find(delegate, ["dragend"], selector),
                                dragcleanup: event.find(delegate, ["dragcleanup"], selector)
                            },
                            destroyed: function() {
                                self.current = null;
                            }
                        }, ev);
                }
            });


        $.extend($.Drag.prototype, {
                setup: function(options, ev) {
                    $.extend(this, options);

                    this.element = $(this.element);
                    this.event = ev;
                    this.moved = false;
                    this.allowOtherDrags = false;
                    var mousemove = bind(this, this.mousemove),
                        mouseup = bind(this, this.mouseup);
                    this._mousemove = mousemove;
                    this._mouseup = mouseup;
                    this._distance = options.distance ? options.distance : 0;

                    //where the mouse is located
                    this.mouseStartPosition = ev.vector();

                    $(document).bind(moveEvent, mousemove);
                    $(document).bind(stopEvent, mouseup);
                    if (supportTouch) {
                        // On touch devices we want to disable scrolling
                        $(document).bind(moveEvent, preventTouchScroll);
                    }

                    if (!this.callEvents('down', this.element, ev)) {
                        this.noSelection(this.delegate);
                        //this is for firefox
                        clearSelection();
                    }
                },



                destroy: function() {
                    // Unbind the mouse handlers attached for dragging
                    $(document).unbind(moveEvent, this._mousemove);
                    $(document).unbind(stopEvent, this._mouseup);
                    if (supportTouch) {
                        // Enable scrolling again for touch devices when the drag is done
                        $(document).unbind(moveEvent, preventTouchScroll);
                    }

                    if (!this.moved) {
                        this.event = this.element = null;
                    }

                    if (!supportTouch) {
                        this.selection(this.delegate);
                    }
                    this.destroyed();
                },
                mousemove: function(docEl, ev) {
                    if (!this.moved) {
                        var dist = Math.sqrt(Math.pow(ev.pageX - this.event.pageX, 2) + Math.pow(ev.pageY - this.event.pageY, 2));
                        // Don't initialize the drag if it hasn't been moved the minimum distance
                        if (dist < this._distance) {
                            return false;
                        }
                        // Otherwise call init and indicate that the drag has moved
                        this.init(this.element, ev);
                        this.moved = true;
                    }

                    this.element.trigger('move', this);
                    var pointer = ev.vector();
                    if (this._start_position && this._start_position.equals(pointer)) {
                        return;
                    }
                    this.draw(pointer, ev);
                },

                mouseup: function(docEl, event) {
                    //if there is a current, we should call its dragstop
                    if (this.moved) {
                        this.end(event);
                    }
                    this.destroy();
                },


                noSelection: function(elm) {
                    elm = elm || this.delegate
                    document.documentElement.onselectstart = function() {
                        // Disables selection
                        return false;
                    };
                    document.documentElement.unselectable = "on";
                    this.selectionDisabled = (this.selectionDisabled ? this.selectionDisabled.add(elm) : $(elm));
                    this.selectionDisabled.css('-moz-user-select', '-moz-none');
                },


                selection: function() {
                    if (this.selectionDisabled) {
                        document.documentElement.onselectstart = function() {};
                        document.documentElement.unselectable = "off";
                        this.selectionDisabled.css('-moz-user-select', '');
                    }
                },

                init: function(element, event) {
                    element = $(element);
                    //the element that has been clicked on
                    var startElement = (this.movingElement = (this.element = $(element)));
                    //if a mousemove has come after the click
                    //if the drag has been cancelled
                    this._cancelled = false;
                    this.event = event;


                    this.mouseElementPosition = this.mouseStartPosition.minus(this.element.offsetv()); //where the mouse is on the Element
                    this.callEvents('init', element, event);

                    // Check what they have set and respond accordingly if they canceled
                    if (this._cancelled === true) {
                        return;
                    }
                    // if they set something else as the element
                    this.startPosition = startElement != this.movingElement ? this.movingElement.offsetv() : this.currentDelta();

                    this.makePositioned(this.movingElement);
                    // Adjust the drag elements z-index to a high value
                    this.oldZIndex = this.movingElement.css('zIndex');
                    this.movingElement.css('zIndex', 1000);
                    if (!this._only && this.constructor.responder) {
                        // calls $.Drop.prototype.compile if there is a drop element
                        this.constructor.responder.compile(event, this);
                    }
                },
                makePositioned: function(that) {
                    var style, pos = that.css('position');

                    // Position properly, set top and left to 0px for Opera
                    if (!pos || pos == 'static') {
                        style = {
                            position: 'relative'
                        };

                        if (window.opera) {
                            style.top = '0px';
                            style.left = '0px';
                        }
                        that.css(style);
                    }
                },
                callEvents: function(type, element, event, drop) {
                    var i, cbs = this.callbacks[this.constructor.lowerName + type];
                    for (i = 0; i < cbs.length; i++) {
                        cbs[i].call(element, event, this, drop);
                    }
                    return cbs.length;
                },

                currentDelta: function() {
                    return new $.Vector(parseInt(this.movingElement.css('left'), 10) || 0, parseInt(this.movingElement.css('top'), 10) || 0);
                },
                //draws the position of the dragmove object
                draw: function(pointer, event) {
                    // only drag if we haven't been cancelled;
                    if (this._cancelled) {
                        return;
                    }
                    clearSelection();

                    // the offset between the mouse pointer and the representative that the user asked for
                    this.location = pointer.minus(this.mouseElementPosition);

                    // call move events
                    this.move(event);
                    if (this._cancelled) {
                        return;
                    }
                    if (!event.isDefaultPrevented()) {
                        this.position(this.location);
                    }

                    // fill in
                    if (!this._only && this.constructor.responder) {
                        this.constructor.responder.show(pointer, this, event);
                    }
                },

                position: function(newOffsetv) { //should draw it on the page
                    var style, dragged_element_css_offset = this.currentDelta(),
                        //  the drag element's current left + top css attributes
                        // the vector between the movingElement's page and css positions
                        // this can be thought of as the original offset
                        dragged_element_position_vector = this.movingElement.offsetv().minus(dragged_element_css_offset);
                    this.required_css_position = newOffsetv.minus(dragged_element_position_vector);

                    this.offsetv = newOffsetv;
                    style = this.movingElement[0].style;
                    if (!this._cancelled && !this._horizontal) {
                        style.top = this.required_css_position.top() + "px";
                    }
                    if (!this._cancelled && !this._vertical) {
                        style.left = this.required_css_position.left() + "px";
                    }
                },
                move: function(event) {
                    this.callEvents('move', this.element, event);
                },
                over: function(event, drop) {
                    this.callEvents('over', this.element, event, drop);
                },
                out: function(event, drop) {
                    this.callEvents('out', this.element, event, drop);
                },

                end: function(event) {
                    // If canceled do nothing
                    if (this._cancelled) {
                        return;
                    }
                    // notify the responder - usually a $.Drop instance
                    if (!this._only && this.constructor.responder) {
                        this.constructor.responder.end(event, this);
                    }

                    this.callEvents('end', this.element, event);

                    if (this._revert) {
                        var self = this;
                        // animate moving back to original position
                        this.movingElement.animate({
                                top: this.startPosition.top() + "px",
                                left: this.startPosition.left() + "px"
                            }, function() {
                                self.cleanup.apply(self, arguments);
                            });
                    } else {
                        this.cleanup(event);
                    }
                    this.event = null;
                },

                cleanup: function(event) {
                    this.movingElement.css({
                            zIndex: this.oldZIndex
                        });
                    if (this.movingElement[0] !== this.element[0] && !this.movingElement.has(this.element[0]).length && !this.element.has(this.movingElement[0]).length) {
                        this.movingElement.css({
                                display: 'none'
                            });
                    }
                    if (this._removeMovingElement) {
                        // Remove the element when using drag.ghost()
                        this.movingElement.remove();
                    }

                    if (event) {
                        this.callEvents('cleanup', this.element, event);
                    }

                    this.movingElement = this.element = this.event = null;
                },

                cancel: function() {
                    this._cancelled = true;
                    if (!this._only && this.constructor.responder) {
                        // clear the drops
                        this.constructor.responder.clear(this.event.vector(), this, this.event);
                    }
                    this.destroy();

                },

                ghost: function(parent) {
                    // create a ghost by cloning the source element and attach the clone to the dom after the source element
                    var ghost = this.movingElement.clone().css('position', 'absolute');
                    if (parent) {
                        $(parent).append(ghost);
                    } else {
                        $(this.movingElement).after(ghost)
                    }
                    ghost.width(this.movingElement.width()).height(this.movingElement.height());
                    // put the ghost in the right location ...
                    ghost.offset(this.movingElement.offset())

                    // store the original element and make the ghost the dragged element
                    this.movingElement = ghost;
                    this.noSelection(ghost)
                    this._removeMovingElement = true;
                    return ghost;
                },

                representative: function(element, offsetX, offsetY) {
                    this._offsetX = offsetX || 0;
                    this._offsetY = offsetY || 0;

                    var p = this.mouseStartPosition;
                    // Just set the representative as the drag element
                    this.movingElement = $(element);
                    this.movingElement.css({
                            top: (p.y() - this._offsetY) + "px",
                            left: (p.x() - this._offsetX) + "px",
                            display: 'block',
                            position: 'absolute'
                        }).show();
                    this.noSelection(this.movingElement)
                    this.mouseElementPosition = new $.Vector(this._offsetX, this._offsetY);
                    return this;
                },

                revert: function(val) {
                    this._revert = val === undefined ? true : val;
                    return this;
                },

                vertical: function() {
                    this._vertical = true;
                    return this;
                },

                horizontal: function() {
                    this._horizontal = true;
                    return this;
                },

                only: function(only) {
                    return (this._only = (only === undefined ? true : only));
                },


                distance: function(val) {
                    if (val !== undefined) {
                        this._distance = val;
                        return this;
                    } else {
                        return this._distance
                    }
                }
            });

        event.setupHelper([

                'dragdown',

                'draginit',

                'dragover',

                'dragmove',

                'dragout',

                'dragend',

                'dragcleanup'
            ], startEvent, function(e) {
                $.Drag.mousedown.call($.Drag, e, this);
            });

        return $;
    })($, __m12, __m13, __m14);

    // ## jquerypp/event/drag/step/step.js
    var __m15 = (function($) {
        var round = function(x, m) {
            return Math.round(x / m) * m;
        }

        $.Drag.prototype.

        step = function(amount, container, center) {
            //on draws ... make sure this happens
            if (typeof amount == 'number') {
                amount = {
                    x: amount,
                    y: amount
                }
            }
            container = container || $(document.body);
            this._step = amount;

            var styles = container.styles("borderTopWidth", "paddingTop", "borderLeftWidth", "paddingLeft");
            var top = parseInt(styles.borderTopWidth) + parseInt(styles.paddingTop),
                left = parseInt(styles.borderLeftWidth) + parseInt(styles.paddingLeft);

            this._step.offset = container.offsetv().plus(left, top);
            this._step.center = center;
            return this;
        };

        (function() {
            var oldPosition = $.Drag.prototype.position;
            $.Drag.prototype.position = function(offsetPositionv) {
                //adjust required_css_position accordingly
                if (this._step) {
                    var step = this._step,
                        center = step.center && step.center.toLowerCase(),
                        movingSize = this.movingElement.dimensionsv('outer'),
                        lot = step.offset.top() - (center && center != 'x' ? movingSize.height() / 2 : 0),
                        lof = step.offset.left() - (center && center != 'y' ? movingSize.width() / 2 : 0);

                    if (this._step.x) {
                        offsetPositionv.left(Math.round(lof + round(offsetPositionv.left() - lof, this._step.x)))
                    }
                    if (this._step.y) {
                        offsetPositionv.top(Math.round(lot + round(offsetPositionv.top() - lot, this._step.y)))
                    }
                }

                oldPosition.call(this, offsetPositionv)
            }
        })();

        return $;
    })($, __m11, __m3);

    // ## jquerypp/event/drag/limit/limit.js
    var __m16 = (function($) {

        $.Drag.prototype

        .limit = function(container, center) {
            //on draws ... make sure this happens
            var styles = container.styles('borderTopWidth', 'paddingTop', 'borderLeftWidth', 'paddingLeft'),
                paddingBorder = new $.Vector(
                    parseInt(styles.borderLeftWidth, 10) + parseInt(styles.paddingLeft, 10) || 0, parseInt(styles.borderTopWidth, 10) + parseInt(styles.paddingTop, 10) || 0);

            this._limit = {
                offset: container.offsetv().plus(paddingBorder),
                size: container.dimensionsv(),
                center: center === true ? 'both' : center
            };
            return this;
        };

        var oldPosition = $.Drag.prototype.position;
        $.Drag.prototype.position = function(offsetPositionv) {
            //adjust required_css_position accordingly
            if (this._limit) {
                var limit = this._limit,
                    center = limit.center && limit.center.toLowerCase(),
                    movingSize = this.movingElement.dimensionsv('outer'),
                    halfHeight = center && center != 'x' ? movingSize.height() / 2 : 0,
                    halfWidth = center && center != 'y' ? movingSize.width() / 2 : 0,
                    lot = limit.offset.top(),
                    lof = limit.offset.left(),
                    height = limit.size.height(),
                    width = limit.size.width();

                //check if we are out of bounds ...
                //above
                if (offsetPositionv.top() + halfHeight < lot) {
                    offsetPositionv.top(lot - halfHeight);
                }
                //below
                if (offsetPositionv.top() + movingSize.height() - halfHeight > lot + height) {
                    offsetPositionv.top(lot + height - movingSize.height() + halfHeight);
                }
                //left
                if (offsetPositionv.left() + halfWidth < lof) {
                    offsetPositionv.left(lof - halfWidth);
                }
                //right
                if (offsetPositionv.left() + movingSize.width() - halfWidth > lof + width) {
                    offsetPositionv.left(lof + width - movingSize.left() + halfWidth);
                }
            }

            oldPosition.call(this, offsetPositionv);
        };

        return $;
    })($, __m11, __m3);

    // ## jquerypp/event/drop/drop.js
    var __m18 = (function($) {
        var event = $.event;

        var eventNames = [

            "dropover",

            "dropon",

            "dropout",

            "dropinit",

            "dropmove",

            "dropend"
        ];


        $.Drop = function(callbacks, element) {
            $.extend(this, callbacks);
            this.element = $(element);
        }
        // add the elements ...
        $.each(eventNames, function() {
            event.special[this] = {
                add: function(handleObj) {
                    //add this element to the compiles list
                    var el = $(this),
                        current = (el.data("dropEventCount") || 0);
                    el.data("dropEventCount", current + 1)
                    if (current == 0) {
                        $.Drop.addElement(this);
                    }
                },
                remove: function() {
                    var el = $(this),
                        current = (el.data("dropEventCount") || 0);
                    el.data("dropEventCount", current - 1)
                    if (current <= 1) {
                        $.Drop.removeElement(this);
                    }
                }
            }
        });

        $.extend($.Drop, {

                lowerName: "drop",
                _rootElements: [], //elements that are listening for drops
                _elements: $(), //elements that can be dropped on
                last_active: [],
                endName: "dropon",
                // adds an element as a 'root' element
                // this element might have events that we need to respond to
                addElement: function(el) {
                    // check other elements
                    for (var i = 0; i < this._rootElements.length; i++) {
                        if (el == this._rootElements[i]) return;
                    }
                    this._rootElements.push(el);
                },
                removeElement: function(el) {
                    for (var i = 0; i < this._rootElements.length; i++) {
                        if (el == this._rootElements[i]) {
                            this._rootElements.splice(i, 1)
                            return;
                        }
                    }
                },

                sortByDeepestChild: function(a, b) {
                    // Use jQuery.compare to compare two elements
                    var compare = a.element.compare(b.element);
                    if (compare & 16 || compare & 4) return 1;
                    if (compare & 8 || compare & 2) return -1;
                    return 0;
                },

                isAffected: function(point, moveable, responder) {
                    return ((responder.element != moveable.element) && (responder.element.within(point[0], point[1], responder._cache).length == 1));
                },

                deactivate: function(responder, mover, event) {
                    mover.out(event, responder)
                    responder.callHandlers(this.lowerName + 'out', responder.element[0], event, mover)
                },

                activate: function(responder, mover, event) { //this is where we should call over
                    mover.over(event, responder)
                    responder.callHandlers(this.lowerName + 'over', responder.element[0], event, mover);
                },
                move: function(responder, mover, event) {
                    responder.callHandlers(this.lowerName + 'move', responder.element[0], event, mover)
                },

                compile: function(event, drag) {
                    // if we called compile w/o a current drag
                    if (!this.dragging && !drag) {
                        return;
                    } else if (!this.dragging) {
                        this.dragging = drag;
                        this.last_active = [];
                    }
                    var el,
                        drops,
                        selector,
                        dropResponders,
                        newEls = [],
                        dragging = this.dragging;

                    // go to each root element and look for drop elements
                    for (var i = 0; i < this._rootElements.length; i++) { //for each element
                        el = this._rootElements[i]

                        // gets something like {"": ["dropinit"], ".foo" : ["dropover","dropmove"] }
                        var drops = $.event.findBySelector(el, eventNames)

                        // get drop elements by selector
                        for (selector in drops) {
                            dropResponders = selector ? jQuery(selector, el) : [el];

                            // for each drop element
                            for (var e = 0; e < dropResponders.length; e++) {

                                // add the callbacks to the element's Data
                                // there already might be data, so we merge it
                                if (this.addCallbacks(dropResponders[e], drops[selector], dragging)) {
                                    newEls.push(dropResponders[e])
                                };
                            }
                        }
                    }
                    // once all callbacks are added, call init on everything ...
                    this.add(newEls, event, dragging)
                },

                // adds the drag callbacks object to the element or merges other callbacks ...
                // returns true or false if the element is new ...
                // onlyNew lets only new elements add themselves
                addCallbacks: function(el, callbacks, onlyNew) {
                    var origData = $.data(el, "_dropData");
                    if (!origData) {
                        $.data(el, "_dropData", new $.Drop(callbacks, el));
                        return true;
                    } else if (!onlyNew) {
                        var origCbs = origData;
                        // merge data
                        for (var eventName in callbacks) {
                            origCbs[eventName] = origCbs[eventName] ?
                                origCbs[eventName].concat(callbacks[eventName]) :
                                callbacks[eventName];
                        }
                        return false;
                    }
                },
                // calls init on each element's drags. 
                // if its cancelled it's removed
                // adds to the current elements ...
                add: function(newEls, event, drag, dragging) {
                    var i = 0,
                        drop;

                    while (i < newEls.length) {
                        drop = $.data(newEls[i], "_dropData");
                        drop.callHandlers(this.lowerName + 'init', newEls[i], event, drag)
                        if (drop._canceled) {
                            newEls.splice(i, 1)
                        } else {
                            i++;
                        }
                    }
                    this._elements.push.apply(this._elements, newEls)
                },
                show: function(point, moveable, event) {
                    var element = moveable.element;
                    if (!this._elements.length) return;

                    var respondable,
                        affected = [],
                        propagate = true,
                        i = 0,
                        j,
                        la,
                        toBeActivated,
                        aff,
                        oldLastActive = this.last_active,
                        responders = [],
                        self = this,
                        drag;

                    // what's still affected ... we can also move element out here
                    while (i < this._elements.length) {
                        drag = $.data(this._elements[i], "_dropData");

                        if (!drag) {
                            this._elements.splice(i, 1)
                        } else {
                            i++;
                            if (self.isAffected(point, moveable, drag)) {
                                affected.push(drag);
                            }
                        }
                    }

                    // we should only trigger on lowest children
                    affected.sort(this.sortByDeepestChild);
                    event.stopRespondPropagate = function() {
                        propagate = false;
                    }

                    toBeActivated = affected.slice();

                    // all these will be active
                    this.last_active = affected;

                    // deactivate everything in last_active that isn't active
                    for (j = 0; j < oldLastActive.length; j++) {
                        la = oldLastActive[j];
                        i = 0;
                        while ((aff = toBeActivated[i])) {
                            if (la == aff) {
                                toBeActivated.splice(i, 1);
                                break;
                            } else {
                                i++;
                            }
                        }
                        if (!aff) {
                            this.deactivate(la, moveable, event);
                        }
                        if (!propagate) return;
                    }
                    for (var i = 0; i < toBeActivated.length; i++) {
                        this.activate(toBeActivated[i], moveable, event);
                        if (!propagate) return;
                    }

                    // activate everything in affected that isn't in last_active
                    for (i = 0; i < affected.length; i++) {
                        this.move(affected[i], moveable, event);

                        if (!propagate) return;
                    }
                },
                end: function(event, moveable) {
                    var la,
                        endName = this.lowerName + 'end',
                        onEvent = $.Event(this.endName, event),
                        dropData;

                    // call dropon
                    // go through the actives ... if you are over one, call dropped on it
                    for (var i = 0; i < this.last_active.length; i++) {
                        la = this.last_active[i]
                        if (this.isAffected(event.vector(), moveable, la) && la[this.endName]) {
                            la.callHandlers(this.endName, null, onEvent, moveable);
                        }

                        if (onEvent.isPropagationStopped()) {
                            break;
                        }
                    }
                    // call dropend
                    for (var r = 0; r < this._elements.length; r++) {
                        dropData = $.data(this._elements[r], "_dropData");
                        dropData && dropData.callHandlers(endName, null, event, moveable);
                    }

                    this.clear();
                },

                clear: function() {
                    this._elements.each(function() {
                        // remove temporary drop data
                        $.removeData(this, "_dropData")
                    })
                    this._elements = $();
                    delete this.dragging;
                }
            })
        $.Drag.responder = $.Drop;

        $.extend($.Drop.prototype, {

                callHandlers: function(method, el, ev, drag) {
                    var length = this[method] ? this[method].length : 0
                    for (var i = 0; i < length; i++) {
                        this[method][i].call(el || this.element[0], ev, this, drag)
                    }
                },

                cache: function(value) {
                    this._cache = value != null ? value : true;
                },

                cancel: function() {
                    this._canceled = true;
                }
            });

        return $;
    })($, __m11, __m8, __m7);

    // ## jquerypp/event/drag/scroll/scroll.js
    var __m17 = (function($) { //needs drop to determine if respondable

        $.Drag.prototype.

        scrolls = function(elements, options) {
            var elements = $(elements);

            for (var i = 0; i < elements.length; i++) {
                this.constructor.responder._elements.push(elements.eq(i).data("_dropData", new $.Scrollable(elements[i], options))[0])
            }
        },

        $.Scrollable = function(element, options) {
            this.element = jQuery(element);
            this.options = $.extend({
                    // when  we should start scrolling
                    distance: 30,
                    // how far we should move
                    delta: function(diff, distance) {
                        return (distance - diff) / 2;
                    },
                    direction: "xy"
                }, options);
            this.x = this.options.direction.indexOf("x") != -1;
            this.y = this.options.direction.indexOf("y") != -1;
        }
        $.extend($.Scrollable.prototype, {
                init: function(element) {
                    this.element = jQuery(element);
                },
                callHandlers: function(method, el, ev, drag) {
                    this[method](el || this.element[0], ev, this, drag)
                },
                dropover: function() {

                },
                dropon: function() {
                    this.clear_timeout();
                },
                dropout: function() {
                    this.clear_timeout();
                },
                dropinit: function() {

                },
                dropend: function() {},
                clear_timeout: function() {
                    if (this.interval) {
                        clearTimeout(this.interval)
                        this.interval = null;
                    }
                },
                distance: function(diff) {
                    return (30 - diff) / 2;
                },
                dropmove: function(el, ev, drop, drag) {

                    //if we were about to call a move, clear it.
                    this.clear_timeout();

                    //position of the mouse
                    var mouse = ev.vector(),

                        //get the object we are going to get the boundries of
                        location_object = $(el == document.documentElement ? window : el),

                        //get the dimension and location of that object
                        dimensions = location_object.dimensionsv('outer'),
                        position = location_object.offsetv(),

                        //how close our mouse is to the boundries
                        bottom = position.y() + dimensions.y() - mouse.y(),
                        top = mouse.y() - position.y(),
                        right = position.x() + dimensions.x() - mouse.x(),
                        left = mouse.x() - position.x(),

                        //how far we should scroll
                        dx = 0,
                        dy = 0,
                        distance = this.options.distance;

                    //check if we should scroll
                    if (bottom < distance && this.y) {
                        dy = this.options.delta(bottom, distance);
                    } else if (top < distance && this.y) {
                        dy = -this.options.delta(top, distance);
                    }

                    if (right < distance && this.options && this.x) {
                        dx = this.options.delta(right, distance);
                    } else if (left < distance && this.x) {
                        dx = -this.options.delta(left, distance);
                    }

                    //if we should scroll
                    if (dx || dy) {
                        //set a timeout that will create a mousemove on that object
                        var self = this;
                        this.interval = setTimeout(function() {
                            self.move($(el), drag.movingElement, dx, dy, ev, ev.clientX, ev.clientY, ev.screenX, ev.screenY)
                        }, 15)
                    }
                },

                move: function(scroll_element, drag_element, dx, dy, ev) {
                    scroll_element.scrollTop(scroll_element.scrollTop() + dy);
                    scroll_element.scrollLeft(scroll_element.scrollLeft() + dx);

                    drag_element.trigger(
                        $.event.fix({
                                type: "mousemove",
                                clientX: ev.clientX,
                                clientY: ev.clientY,
                                screenX: ev.screenX,
                                screenY: ev.screenY,
                                pageX: ev.pageX,
                                pageY: ev.pageY
                            }))
                    //drag_element.synthetic('mousemove',{clientX: x, clientY: y, screenX: sx, screenY: sy})
                }
            })

        return $;
    })($, __m18);

    // ## jquerypp/event/drag/drag.js
    var __m10 = (function($) {
        return $;
    })($, __m11, __m15, __m16, __m17);

    // ## jquerypp/event/fastfix/fastfix.js
    var __m19 = (function($) {
        // http://bitovi.com/blog/2012/04/faster-jquery-event-fix.html
        // https://gist.github.com/2377196

        // IE 8 has Object.defineProperty but it only defines DOM Nodes. According to
        // http://kangax.github.com/es5-compat-table/#define-property-ie-note
        // All browser that have Object.defineProperties also support Object.defineProperty properly
        if (Object.defineProperties) {
            var
            // Use defineProperty on an object to set the value and return it
            set = function(obj, prop, val) {
                if (val !== undefined) {
                    Object.defineProperty(obj, prop, {
                            value: val
                        });
                }
                return val;
            },
                // special converters
                special = {
                    pageX: function(original) {
                        if (!original) {
                            return;
                        }

                        var eventDoc = this.target.ownerDocument || document;
                        doc = eventDoc.documentElement;
                        body = eventDoc.body;
                        return original.clientX + (doc && doc.scrollLeft || body && body.scrollLeft || 0) - (doc && doc.clientLeft || body && body.clientLeft || 0);
                    },
                    pageY: function(original) {
                        if (!original) {
                            return;
                        }

                        var eventDoc = this.target.ownerDocument || document;
                        doc = eventDoc.documentElement;
                        body = eventDoc.body;
                        return original.clientY + (doc && doc.scrollTop || body && body.scrollTop || 0) - (doc && doc.clientTop || body && body.clientTop || 0);
                    },
                    relatedTarget: function(original) {
                        if (!original) {
                            return;
                        }

                        return original.fromElement === this.target ? original.toElement : original.fromElement;
                    },
                    metaKey: function(originalEvent) {
                        if (!originalEvent) {
                            return;
                        }
                        return originalEvent.ctrlKey;
                    },
                    which: function(original) {
                        if (!original) {
                            return;
                        }

                        return original.charCode != null ? original.charCode : original.keyCode;
                    }
                };

            // Get all properties that should be mapped
            $.each($.event.keyHooks.props.concat($.event.mouseHooks.props).concat($.event.props), function(i, prop) {
                if (prop !== "target") {
                    (function() {
                        Object.defineProperty($.Event.prototype, prop, {
                                get: function() {
                                    // get the original value, undefined when there is no original event
                                    var originalValue = this.originalEvent && this.originalEvent[prop];
                                    // overwrite getter lookup
                                    return this['_' + prop] !== undefined ? this['_' + prop] : set(this, prop,
                                        // if we have a special function and no value
                                        special[prop] && originalValue === undefined ?
                                        // call the special function
                                        special[prop].call(this, this.originalEvent) :
                                        // use the original value
                                        originalValue)
                                },
                                set: function(newValue) {
                                    // Set the property with underscore prefix
                                    this['_' + prop] = newValue;
                                }
                            });
                    })();
                }
            });

            $.event.fix = function(event) {
                if (event[$.expando]) {
                    return event;
                }
                // Create a jQuery event with at minimum a target and type set
                var originalEvent = event,
                    event = $.Event(originalEvent);
                event.target = originalEvent.target;
                // Fix target property, if necessary (#1925, IE 6/7/8 & Safari2)
                if (!event.target) {
                    event.target = originalEvent.srcElement || document;
                }

                // Target should not be a text node (#504, Safari)
                if (event.target.nodeType === 3) {
                    event.target = event.target.parentNode;
                }

                return event;
            }
        }

        return $;
    })($);

    // ## jquerypp/event/resize/resize.js
    var __m20 = (function($) {
        var
        // bind on the window window resizes to happen
        win = $(window),
            windowWidth = 0,
            windowHeight = 0,
            timer;

        $(function() {
            windowWidth = win.width();
            windowHeight = win.height();
        });

        $.event.reverse('resize', {
                handler: function(ev, data) {
                    var isWindow = this === window;

                    // if we are the window and a real resize has happened
                    // then we check if the dimensions actually changed
                    // if they did, we will wait a brief timeout and
                    // trigger resize on the window
                    // this is for IE, to prevent window resize 'infinate' loop issues
                    if (isWindow && ev.originalEvent) {
                        var width = win.width(),
                            height = win.height();

                        if ((width != windowWidth || height != windowHeight)) {
                            //update the new dimensions
                            windowWidth = width;
                            windowHeight = height;
                            clearTimeout(timer)
                            timer = setTimeout(function() {
                                win.trigger("resize");
                            }, 1);

                        }
                        return true;
                    }
                }
            });

        return $;
    })(__m14);
})(jQuery);