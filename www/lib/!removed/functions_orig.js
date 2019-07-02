(function ($) {

    var defaultDiacriticsRemovalMap = [
        {'base': 'ö', 'letters': /[\u00f6]/g},
        {'base': 'ü', 'letters': /[\u00fc]/g},
        {'base': 'á', 'letters': /[\u00e1]/g},
        {'base': 'ó', 'letters': /[\u00f3]/g},
        {'base': 'é', 'letters': /[\u00e9]/g}
        /*
         {'base':'A', 'letters':/[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g},
         {'base':'AA','letters':/[\uA732]/g},
         {'base':'AE','letters':/[\u00C6\u01FC\u01E2]/g},
         {'base':'AO','letters':/[\uA734]/g},
         {'base':'AU','letters':/[\uA736]/g},
         {'base':'AV','letters':/[\uA738\uA73A]/g},
         {'base':'AY','letters':/[\uA73C]/g},
         {'base':'B', 'letters':/[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g},
         {'base':'C', 'letters':/[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g},
         {'base':'D', 'letters':/[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g},
         {'base':'DZ','letters':/[\u01F1\u01C4]/g},
         {'base':'Dz','letters':/[\u01F2\u01C5]/g},
         {'base':'E', 'letters':/[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g},
         {'base':'F', 'letters':/[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},
         {'base':'G', 'letters':/[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g},
         {'base':'H', 'letters':/[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g},
         {'base':'I', 'letters':/[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g},
         {'base':'J', 'letters':/[\u004A\u24BF\uFF2A\u0134\u0248]/g},
         {'base':'K', 'letters':/[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g},
         {'base':'L', 'letters':/[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g},
         {'base':'LJ','letters':/[\u01C7]/g},
         {'base':'Lj','letters':/[\u01C8]/g},
         {'base':'M', 'letters':/[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g},
         {'base':'N', 'letters':/[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g},
         {'base':'NJ','letters':/[\u01CA]/g},
         {'base':'Nj','letters':/[\u01CB]/g},
         {'base':'O', 'letters':/[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g},
         {'base':'OI','letters':/[\u01A2]/g},
         {'base':'OO','letters':/[\uA74E]/g},
         {'base':'OU','letters':/[\u0222]/g},
         {'base':'P', 'letters':/[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g},
         {'base':'Q', 'letters':/[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g},
         {'base':'R', 'letters':/[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g},
         {'base':'S', 'letters':/[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g},
         {'base':'T', 'letters':/[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g},
         {'base':'TZ','letters':/[\uA728]/g},
         {'base':'U', 'letters':/[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g},
         {'base':'V', 'letters':/[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g},
         {'base':'VY','letters':/[\uA760]/g},
         {'base':'W', 'letters':/[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g},
         {'base':'X', 'letters':/[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g},
         {'base':'Y', 'letters':/[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g},
         {'base':'Z', 'letters':/[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g},
         {'base':'a', 'letters':/[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g},
         {'base':'aa','letters':/[\uA733]/g},
         {'base':'ae','letters':/[\u00E6\u01FD\u01E3]/g},
         {'base':'ao','letters':/[\uA735]/g},
         {'base':'au','letters':/[\uA737]/g},
         {'base':'av','letters':/[\uA739\uA73B]/g},
         {'base':'ay','letters':/[\uA73D]/g},
         {'base':'b', 'letters':/[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g},
         {'base':'c', 'letters':/[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g},
         {'base':'d', 'letters':/[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g},
         {'base':'dz','letters':/[\u01F3\u01C6]/g},
         {'base':'e', 'letters':/[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g},
         {'base':'f', 'letters':/[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g},
         {'base':'g', 'letters':/[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g},
         {'base':'h', 'letters':/[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g},
         {'base':'hv','letters':/[\u0195]/g},
         {'base':'i', 'letters':/[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g},
         {'base':'j', 'letters':/[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g},
         {'base':'k', 'letters':/[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g},
         {'base':'l', 'letters':/[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g},
         {'base':'lj','letters':/[\u01C9]/g},
         {'base':'m', 'letters':/[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g},
         {'base':'n', 'letters':/[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g},
         {'base':'nj','letters':/[\u01CC]/g},
         //{'base':'o', 'letters':/[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g},
         {'base':'oi','letters':/[\u01A3]/g},
         {'base':'ou','letters':/[\u0223]/g},
         {'base':'oo','letters':/[\uA74F]/g},
         {'base':'p','letters':/[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g},
         {'base':'q','letters':/[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g},
         {'base':'r','letters':/[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g},
         {'base':'s','letters':/[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g},
         {'base':'t','letters':/[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g},
         {'base':'tz','letters':/[\uA729]/g},
         //{'base':'u','letters':/[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g},
         {'base':'v','letters':/[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g},
         {'base':'vy','letters':/[\uA761]/g},
         {'base':'w','letters':/[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g},
         {'base':'x','letters':/[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},
         {'base':'y','letters':/[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g},
         {'base':'z','letters':/[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g}
         */
    ];
    var changes = false;
    var removeDiacritics = function (str) {
        if (!changes) {
            changes = defaultDiacriticsRemovalMap;
        }
        for (var i = 0; i < changes.length; i++) {
            str = str.replace(changes[i].letters, changes[i].base);
        }
        return str;
    };

    var Latinise = {};
    Latinise.latin_map = {"Á": "A", "Ă": "A", "Ắ": "A", "Ặ": "A", "Ằ": "A", "Ẳ": "A", "Ẵ": "A", "Ǎ": "A", "Â": "A", "Ấ": "A", "Ậ": "A", "Ầ": "A", "Ẩ": "A", "Ẫ": "A", "Ä": "A", "Ǟ": "A", "Ȧ": "A", "Ǡ": "A", "Ạ": "A", "Ȁ": "A", "À": "A", "Ả": "A", "Ȃ": "A", "Ā": "A", "Ą": "A", "Å": "A", "Ǻ": "A", "Ḁ": "A", "Ⱥ": "A", "Ã": "A", "Ꜳ": "AA", "Æ": "AE", "Ǽ": "AE", "Ǣ": "AE", "Ꜵ": "AO", "Ꜷ": "AU", "Ꜹ": "AV", "Ꜻ": "AV", "Ꜽ": "AY", "Ḃ": "B", "Ḅ": "B", "Ɓ": "B", "Ḇ": "B", "Ƀ": "B", "Ƃ": "B", "Ć": "C", "Č": "C", "Ç": "C", "Ḉ": "C", "Ĉ": "C", "Ċ": "C", "Ƈ": "C", "Ȼ": "C", "Ď": "D", "Ḑ": "D", "Ḓ": "D", "Ḋ": "D", "Ḍ": "D", "Ɗ": "D", "Ḏ": "D", "ǲ": "D", "ǅ": "D", "Đ": "D", "Ƌ": "D", "Ǳ": "DZ", "Ǆ": "DZ", "É": "E", "Ĕ": "E", "Ě": "E", "Ȩ": "E", "Ḝ": "E", "Ê": "E", "Ế": "E", "Ệ": "E", "Ề": "E", "Ể": "E", "Ễ": "E", "Ḙ": "E", "Ë": "E", "Ė": "E", "Ẹ": "E", "Ȅ": "E", "È": "E", "Ẻ": "E", "Ȇ": "E", "Ē": "E", "Ḗ": "E", "Ḕ": "E", "Ę": "E", "Ɇ": "E", "Ẽ": "E", "Ḛ": "E", "Ꝫ": "ET", "Ḟ": "F", "Ƒ": "F", "Ǵ": "G", "Ğ": "G", "Ǧ": "G", "Ģ": "G", "Ĝ": "G", "Ġ": "G", "Ɠ": "G", "Ḡ": "G", "Ǥ": "G", "Ḫ": "H", "Ȟ": "H", "Ḩ": "H", "Ĥ": "H", "Ⱨ": "H", "Ḧ": "H", "Ḣ": "H", "Ḥ": "H", "Ħ": "H", "Í": "I", "Ĭ": "I", "Ǐ": "I", "Î": "I", "Ï": "I", "Ḯ": "I", "İ": "I", "Ị": "I", "Ȉ": "I", "Ì": "I", "Ỉ": "I", "Ȋ": "I", "Ī": "I", "Į": "I", "Ɨ": "I", "Ĩ": "I", "Ḭ": "I", "Ꝺ": "D", "Ꝼ": "F", "Ᵹ": "G", "Ꞃ": "R", "Ꞅ": "S", "Ꞇ": "T", "Ꝭ": "IS", "Ĵ": "J", "Ɉ": "J", "Ḱ": "K", "Ǩ": "K", "Ķ": "K", "Ⱪ": "K", "Ꝃ": "K", "Ḳ": "K", "Ƙ": "K", "Ḵ": "K", "Ꝁ": "K", "Ꝅ": "K", "Ĺ": "L", "Ƚ": "L", "Ľ": "L", "Ļ": "L", "Ḽ": "L", "Ḷ": "L", "Ḹ": "L", "Ⱡ": "L", "Ꝉ": "L", "Ḻ": "L", "Ŀ": "L", "Ɫ": "L", "ǈ": "L", "Ł": "L", "Ǉ": "LJ", "Ḿ": "M", "Ṁ": "M", "Ṃ": "M", "Ɱ": "M", "Ń": "N", "Ň": "N", "Ņ": "N", "Ṋ": "N", "Ṅ": "N", "Ṇ": "N", "Ǹ": "N", "Ɲ": "N", "Ṉ": "N", "Ƞ": "N", "ǋ": "N", "Ñ": "N", "Ǌ": "NJ", "Ó": "O", "Ŏ": "O", "Ǒ": "O", "Ô": "O", "Ố": "O", "Ộ": "O", "Ồ": "O", "Ổ": "O", "Ỗ": "O", "Ö": "O", "Ȫ": "O", "Ȯ": "O", "Ȱ": "O", "Ọ": "O", "Ő": "O", "Ȍ": "O", "Ò": "O", "Ỏ": "O", "Ơ": "O", "Ớ": "O", "Ợ": "O", "Ờ": "O", "Ở": "O", "Ỡ": "O", "Ȏ": "O", "Ꝋ": "O", "Ꝍ": "O", "Ō": "O", "Ṓ": "O", "Ṑ": "O", "Ɵ": "O", "Ǫ": "O", "Ǭ": "O", "Ø": "O", "Ǿ": "O", "Õ": "O", "Ṍ": "O", "Ṏ": "O", "Ȭ": "O", "Ƣ": "OI", "Ꝏ": "OO", "Ɛ": "E", "Ɔ": "O", "Ȣ": "OU", "Ṕ": "P", "Ṗ": "P", "Ꝓ": "P", "Ƥ": "P", "Ꝕ": "P", "Ᵽ": "P", "Ꝑ": "P", "Ꝙ": "Q", "Ꝗ": "Q", "Ŕ": "R", "Ř": "R", "Ŗ": "R", "Ṙ": "R", "Ṛ": "R", "Ṝ": "R", "Ȑ": "R", "Ȓ": "R", "Ṟ": "R", "Ɍ": "R", "Ɽ": "R", "Ꜿ": "C", "Ǝ": "E", "Ś": "S", "Ṥ": "S", "Š": "S", "Ṧ": "S", "Ş": "S", "Ŝ": "S", "Ș": "S", "Ṡ": "S", "Ṣ": "S", "Ṩ": "S", "Ť": "T", "Ţ": "T", "Ṱ": "T", "Ț": "T", "Ⱦ": "T", "Ṫ": "T", "Ṭ": "T", "Ƭ": "T", "Ṯ": "T", "Ʈ": "T", "Ŧ": "T", "Ɐ": "A", "Ꞁ": "L", "Ɯ": "M", "Ʌ": "V", "Ꜩ": "TZ", "Ú": "U", "Ŭ": "U", "Ǔ": "U", "Û": "U", "Ṷ": "U", "Ü": "U", "Ǘ": "U", "Ǚ": "U", "Ǜ": "U", "Ǖ": "U", "Ṳ": "U", "Ụ": "U", "Ű": "U", "Ȕ": "U", "Ù": "U", "Ủ": "U", "Ư": "U", "Ứ": "U", "Ự": "U", "Ừ": "U", "Ử": "U", "Ữ": "U", "Ȗ": "U", "Ū": "U", "Ṻ": "U", "Ų": "U", "Ů": "U", "Ũ": "U", "Ṹ": "U", "Ṵ": "U", "Ꝟ": "V", "Ṿ": "V", "Ʋ": "V", "Ṽ": "V", "Ꝡ": "VY", "Ẃ": "W", "Ŵ": "W", "Ẅ": "W", "Ẇ": "W", "Ẉ": "W", "Ẁ": "W", "Ⱳ": "W", "Ẍ": "X", "Ẋ": "X", "Ý": "Y", "Ŷ": "Y", "Ÿ": "Y", "Ẏ": "Y", "Ỵ": "Y", "Ỳ": "Y", "Ƴ": "Y", "Ỷ": "Y", "Ỿ": "Y", "Ȳ": "Y", "Ɏ": "Y", "Ỹ": "Y", "Ź": "Z", "Ž": "Z", "Ẑ": "Z", "Ⱬ": "Z", "Ż": "Z", "Ẓ": "Z", "Ȥ": "Z", "Ẕ": "Z", "Ƶ": "Z", "Ĳ": "IJ", "Œ": "OE", "ᴀ": "A", "ᴁ": "AE", "ʙ": "B", "ᴃ": "B", "ᴄ": "C", "ᴅ": "D", "ᴇ": "E", "ꜰ": "F", "ɢ": "G", "ʛ": "G", "ʜ": "H", "ɪ": "I", "ʁ": "R", "ᴊ": "J", "ᴋ": "K", "ʟ": "L", "ᴌ": "L", "ᴍ": "M", "ɴ": "N", "ᴏ": "O", "ɶ": "OE", "ᴐ": "O", "ᴕ": "OU", "ᴘ": "P", "ʀ": "R", "ᴎ": "N", "ᴙ": "R", "ꜱ": "S", "ᴛ": "T", "ⱻ": "E", "ᴚ": "R", "ᴜ": "U", "ᴠ": "V", "ᴡ": "W", "ʏ": "Y", "ᴢ": "Z", "á": "a", "ă": "a", "ắ": "a", "ặ": "a", "ằ": "a", "ẳ": "a", "ẵ": "a", "ǎ": "a", "â": "a", "ấ": "a", "ậ": "a", "ầ": "a", "ẩ": "a", "ẫ": "a", "ä": "a", "ǟ": "a", "ȧ": "a", "ǡ": "a", "ạ": "a", "ȁ": "a", "à": "a", "ả": "a", "ȃ": "a", "ā": "a", "ą": "a", "ᶏ": "a", "ẚ": "a", "å": "a", "ǻ": "a", "ḁ": "a", "ⱥ": "a", "ã": "a", "ꜳ": "aa", "æ": "ae", "ǽ": "ae", "ǣ": "ae", "ꜵ": "ao", "ꜷ": "au", "ꜹ": "av", "ꜻ": "av", "ꜽ": "ay", "ḃ": "b", "ḅ": "b", "ɓ": "b", "ḇ": "b", "ᵬ": "b", "ᶀ": "b", "ƀ": "b", "ƃ": "b", "ɵ": "o", "ć": "c", "č": "c", "ç": "c", "ḉ": "c", "ĉ": "c", "ɕ": "c", "ċ": "c", "ƈ": "c", "ȼ": "c", "ď": "d", "ḑ": "d", "ḓ": "d", "ȡ": "d", "ḋ": "d", "ḍ": "d", "ɗ": "d", "ᶑ": "d", "ḏ": "d", "ᵭ": "d", "ᶁ": "d", "đ": "d", "ɖ": "d", "ƌ": "d", "ı": "i", "ȷ": "j", "ɟ": "j", "ʄ": "j", "ǳ": "dz", "ǆ": "dz", "é": "e", "ĕ": "e", "ě": "e", "ȩ": "e", "ḝ": "e", "ê": "e", "ế": "e", "ệ": "e", "ề": "e", "ể": "e", "ễ": "e", "ḙ": "e", "ë": "e", "ė": "e", "ẹ": "e", "ȅ": "e", "è": "e", "ẻ": "e", "ȇ": "e", "ē": "e", "ḗ": "e", "ḕ": "e", "ⱸ": "e", "ę": "e", "ᶒ": "e", "ɇ": "e", "ẽ": "e", "ḛ": "e", "ꝫ": "et", "ḟ": "f", "ƒ": "f", "ᵮ": "f", "ᶂ": "f", "ǵ": "g", "ğ": "g", "ǧ": "g", "ģ": "g", "ĝ": "g", "ġ": "g", "ɠ": "g", "ḡ": "g", "ᶃ": "g", "ǥ": "g", "ḫ": "h", "ȟ": "h", "ḩ": "h", "ĥ": "h", "ⱨ": "h", "ḧ": "h", "ḣ": "h", "ḥ": "h", "ɦ": "h", "ẖ": "h", "ħ": "h", "ƕ": "hv", "í": "i", "ĭ": "i", "ǐ": "i", "î": "i", "ï": "i", "ḯ": "i", "ị": "i", "ȉ": "i", "ì": "i", "ỉ": "i", "ȋ": "i", "ī": "i", "į": "i", "ᶖ": "i", "ɨ": "i", "ĩ": "i", "ḭ": "i", "ꝺ": "d", "ꝼ": "f", "ᵹ": "g", "ꞃ": "r", "ꞅ": "s", "ꞇ": "t", "ꝭ": "is", "ǰ": "j", "ĵ": "j", "ʝ": "j", "ɉ": "j", "ḱ": "k", "ǩ": "k", "ķ": "k", "ⱪ": "k", "ꝃ": "k", "ḳ": "k", "ƙ": "k", "ḵ": "k", "ᶄ": "k", "ꝁ": "k", "ꝅ": "k", "ĺ": "l", "ƚ": "l", "ɬ": "l", "ľ": "l", "ļ": "l", "ḽ": "l", "ȴ": "l", "ḷ": "l", "ḹ": "l", "ⱡ": "l", "ꝉ": "l", "ḻ": "l", "ŀ": "l", "ɫ": "l", "ᶅ": "l", "ɭ": "l", "ł": "l", "ǉ": "lj", "ſ": "s", "ẜ": "s", "ẛ": "s", "ẝ": "s", "ḿ": "m", "ṁ": "m", "ṃ": "m", "ɱ": "m", "ᵯ": "m", "ᶆ": "m", "ń": "n", "ň": "n", "ņ": "n", "ṋ": "n", "ȵ": "n", "ṅ": "n", "ṇ": "n", "ǹ": "n", "ɲ": "n", "ṉ": "n", "ƞ": "n", "ᵰ": "n", "ᶇ": "n", "ɳ": "n", "ñ": "n", "ǌ": "nj", "ó": "o", "ŏ": "o", "ǒ": "o", "ô": "o", "ố": "o", "ộ": "o", "ồ": "o", "ổ": "o", "ỗ": "o", "ö": "o", "ȫ": "o", "ȯ": "o", "ȱ": "o", "ọ": "o", "ő": "o", "ȍ": "o", "ò": "o", "ỏ": "o", "ơ": "o", "ớ": "o", "ợ": "o", "ờ": "o", "ở": "o", "ỡ": "o", "ȏ": "o", "ꝋ": "o", "ꝍ": "o", "ⱺ": "o", "ō": "o", "ṓ": "o", "ṑ": "o", "ǫ": "o", "ǭ": "o", "ø": "o", "ǿ": "o", "õ": "o", "ṍ": "o", "ṏ": "o", "ȭ": "o", "ƣ": "oi", "ꝏ": "oo", "ɛ": "e", "ᶓ": "e", "ɔ": "o", "ᶗ": "o", "ȣ": "ou", "ṕ": "p", "ṗ": "p", "ꝓ": "p", "ƥ": "p", "ᵱ": "p", "ᶈ": "p", "ꝕ": "p", "ᵽ": "p", "ꝑ": "p", "ꝙ": "q", "ʠ": "q", "ɋ": "q", "ꝗ": "q", "ŕ": "r", "ř": "r", "ŗ": "r", "ṙ": "r", "ṛ": "r", "ṝ": "r", "ȑ": "r", "ɾ": "r", "ᵳ": "r", "ȓ": "r", "ṟ": "r", "ɼ": "r", "ᵲ": "r", "ᶉ": "r", "ɍ": "r", "ɽ": "r", "ↄ": "c", "ꜿ": "c", "ɘ": "e", "ɿ": "r", "ś": "s", "ṥ": "s", "š": "s", "ṧ": "s", "ş": "s", "ŝ": "s", "ș": "s", "ṡ": "s", "ṣ": "s", "ṩ": "s", "ʂ": "s", "ᵴ": "s", "ᶊ": "s", "ȿ": "s", "ɡ": "g", "ᴑ": "o", "ᴓ": "o", "ᴝ": "u", "ť": "t", "ţ": "t", "ṱ": "t", "ț": "t", "ȶ": "t", "ẗ": "t", "ⱦ": "t", "ṫ": "t", "ṭ": "t", "ƭ": "t", "ṯ": "t", "ᵵ": "t", "ƫ": "t", "ʈ": "t", "ŧ": "t", "ᵺ": "th", "ɐ": "a", "ᴂ": "ae", "ǝ": "e", "ᵷ": "g", "ɥ": "h", "ʮ": "h", "ʯ": "h", "ᴉ": "i", "ʞ": "k", "ꞁ": "l", "ɯ": "m", "ɰ": "m", "ᴔ": "oe", "ɹ": "r", "ɻ": "r", "ɺ": "r", "ⱹ": "r", "ʇ": "t", "ʌ": "v", "ʍ": "w", "ʎ": "y", "ꜩ": "tz", "ú": "u", "ŭ": "u", "ǔ": "u", "û": "u", "ṷ": "u", "ü": "u", "ǘ": "u", "ǚ": "u", "ǜ": "u", "ǖ": "u", "ṳ": "u", "ụ": "u", "ű": "u", "ȕ": "u", "ù": "u", "ủ": "u", "ư": "u", "ứ": "u", "ự": "u", "ừ": "u", "ử": "u", "ữ": "u", "ȗ": "u", "ū": "u", "ṻ": "u", "ų": "u", "ᶙ": "u", "ů": "u", "ũ": "u", "ṹ": "u", "ṵ": "u", "ᵫ": "ue", "ꝸ": "um", "ⱴ": "v", "ꝟ": "v", "ṿ": "v", "ʋ": "v", "ᶌ": "v", "ⱱ": "v", "ṽ": "v", "ꝡ": "vy", "ẃ": "w", "ŵ": "w", "ẅ": "w", "ẇ": "w", "ẉ": "w", "ẁ": "w", "ⱳ": "w", "ẘ": "w", "ẍ": "x", "ẋ": "x", "ᶍ": "x", "ý": "y", "ŷ": "y", "ÿ": "y", "ẏ": "y", "ỵ": "y", "ỳ": "y", "ƴ": "y", "ỷ": "y", "ỿ": "y", "ȳ": "y", "ẙ": "y", "ɏ": "y", "ỹ": "y", "ź": "z", "ž": "z", "ẑ": "z", "ʑ": "z", "ⱬ": "z", "ż": "z", "ẓ": "z", "ȥ": "z", "ẕ": "z", "ᵶ": "z", "ᶎ": "z", "ʐ": "z", "ƶ": "z", "ɀ": "z", "ﬀ": "ff", "ﬃ": "ffi", "ﬄ": "ffl", "ﬁ": "fi", "ﬂ": "fl", "ĳ": "ij", "œ": "oe", "ﬆ": "st", "ₐ": "a", "ₑ": "e", "ᵢ": "i", "ⱼ": "j", "ₒ": "o", "ᵣ": "r", "ᵤ": "u", "ᵥ": "v", "ₓ": "x"};
    String.prototype.latinise = function () {
        return this.replace(/[^A-Za-z0-9\[\] ]/g, function (a) {
            return Latinise.latin_map[a] || a
        })
    };
    String.prototype.latinize = String.prototype.latinise;
    String.prototype.isLatin = function () {
        return this == this.latinise()
    }

    convertDoname = function (str) {
        return str.latinise().toLowerCase().replace(' ', '')
    };

    sendMessage = function (messageType, message) {
        if ($('#messageHolder').length = 0) {
            $('#ContentEditor').prepend('<div id="messageHolder"></div>');
        }
        var $container = $('#messageHolder');
        $container.empty();
        $container.append('<div class="alert fade"><div class="message"></div></div>');
        var $messageDiv = $('#messageHolder .message');
        var $alertDiv = $('#messageHolder .alert');
        $messageDiv.html(message);
        $alertDiv.addClass(messageType).addClass('in');
        setTimeout(function () {
            $alertDiv.remove();
        }, 2000);
    };

    romanize = function (num) {
        if (!+num)
            return false;
        var digits = String(+num).split(""),
            key = ["", "C", "CC", "CCC", "CD", "D", "DC", "DCC", "DCCC", "CM",
                "", "X", "XX", "XXX", "XL", "L", "LX", "LXX", "LXXX", "XC",
                "", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX"],
            roman = "",
            i = 3;
        while (i--)
            roman = (key[+digits.pop() + (i * 10)] || "") + roman;
        return Array(+digits.join("") + 1).join("M") + roman;
    };

//mymedia & editor
    my_implode_js = function (separator, array) {
        var temp = '';
        for (var i = 0; i < array.length; i++) {
            temp += array[i]
            if (i != array.length - 1) {
                temp += separator;
            }
        }//end of the for loop
        return temp;
    };

    Date.prototype.customFormat = function (formatString) {
        var YYYY, YY, MMMM, MMM, MM, M, DDDD, DDD, DD, D, hhh, hh, h, mm, m, ss, s, ampm, AMPM, dMod, th;
        var dateObject = this;
        YY = ((YYYY = dateObject.getFullYear()) + "").slice(-2);
        MM = (M = dateObject.getMonth() + 1) < 10 ? ('0' + M) : M;
        MMM = (MMMM = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][M - 1]).substring(0, 3);
        DD = (D = dateObject.getDate()) < 10 ? ('0' + D) : D;
        DDD = (DDDD = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"][dateObject.getDay()]).substring(0, 3);
        th = (D >= 10 && D <= 20) ? 'th' : ((dMod = D % 10) == 1) ? 'st' : (dMod == 2) ? 'nd' : (dMod == 3) ? 'rd' : 'th';
        formatString = formatString.replace("#YYYY#", YYYY).replace("#YY#", YY).replace("#MMMM#", MMMM).replace("#MMM#", MMM).replace("#MM#", MM).replace("#M#", M).replace("#DDDD#", DDDD).replace("#DDD#", DDD).replace("#DD#", DD).replace("#D#", D).replace("#th#", th);

        h = (hhh = dateObject.getHours());
        if (h == 0) h = 24;
        if (h > 12) h -= 12;
        hh = h < 10 ? ('0' + h) : h;
        AMPM = (ampm = hhh < 12 ? 'am' : 'pm').toUpperCase();
        mm = (m = dateObject.getMinutes()) < 10 ? ('0' + m) : m;
        ss = (s = dateObject.getSeconds()) < 10 ? ('0' + s) : s;
        return formatString.replace("#hhh#", hhh).replace("#hh#", hh).replace("#h#", h).replace("#mm#", mm).replace("#m#", m).replace("#ss#", ss).replace("#s#", s).replace("#ampm#", ampm).replace("#AMPM#", AMPM);
    };

    $.validator.addMethod(
        "dateHU",
        function (value, element) {
            var check = false;
            var re = /^\d{4}\.\d{1,2}\.\d{1,2}$/;
            if (re.test(value)) {
                var adata = value.split('.');
                var mm = parseInt(adata[1], 10); // was gg (giorno / day)
                var dd = parseInt(adata[2], 10); // was mm (mese / month)
                var yyyy = parseInt(adata[0], 10); // was aaaa (anno / year)
                var xdata = new Date(yyyy, mm - 1, dd);
                if (( xdata.getFullYear() == yyyy ) && ( xdata.getMonth() == mm - 1 ) && ( xdata.getDate() == dd ))
                    check = true;
                else
                    check = false;
            } else
                check = false;
            return this.optional(element) || check;
        },
        "Please enter a valid date (yyyy.mm.dd)"
    );

    $.fn.extend({
        //pass the options variable to the function
        confirmModal: function (options) {

            var defaults = {
                heading: 'Please confirm',
                body: 'Body contents',
                text: 'Save',
                page: '',
                type: '',
                callback: null,
                cancel: false,
                canceltext: 'Cancel'
            };

            var options = $.extend(defaults, options);

            var html = '<div class="modal styled" id="myModal"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>' +
                '<h4>#Heading#</h4></div><div class="modal-body' + ((options.type == 'question') ? ' question' : '') + '">' +
                '#Body#</div><div class="modal-footer">';

            if (options.cancel)
                html += '<button class="btn btn-dark" id="cancelBtn">#Canceltext#</button>';

            html += '<button class="btn btn-dark calltoaction" id="confirmYesBtn">#Text#</button>';
            html = html.replace('#Heading#', options.heading).replace('#Body#', options.body).replace('#Text#', options.text);

            if (options.cancel)
                html = html.replace('#Canceltext#', options.canceltext)

            $(this).html(html);

            $(':input').bind('keypress', function (e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (code == 13) return false;
            });
            console.log('confirmmodal')
//console.log(options.callback);
//console.log(options.type);
            if (options.type !== 'question')
                $(this).find('.modal-body').slimScroll({height: '300px', width: '550px', allowPageScroll: false, alwaysVisible: true});
//console.log(options.page);
            switch (options.page) {
                case 'users':
                    $(this).find('.typeahead').typeahead({
                        minLength: 2,
                        source: function (query, process) {
                            process(
                                $.parseJSON($.ajax({
                                    url: "/crawl?/process/users/typeahead/",
                                    data: {form: $('#usersform').serializeArray(), query: query},
                                    type: 'POST',
                                    dataType: 'json',
                                    async: false
                                }).responseText)
                            );
                        }
                    });
                    //$("#newuser").validate(newuserValidate);
                    break;
            }
            $(this).modal('show');
            var context = $(this);
            $('#confirmYesBtn', this).click(function () {
                if (options.callback != null) {
                    options.callback();
                    if (options.page !== 'users')
                        $(context).modal('hide');
                }
            });
            $('#cancelBtn', this).click(function () {

                $(context).modal('hide');
            });
        }
    });

    /*
     @param:  values -> array (form, users, message, type)
     settings -> array (url, responseType, type )
     @return  json or html
     */
    sendEmail = function (values, settings) {
        settings.url = "/crawl?/process/message/";
        settings.data = values;
        var data = getJsonData(settings);
        return data;
    };

    secondsToTime = function (secs) {
        var hours = Math.floor(secs / (60 * 60));
        var divisor_for_minutes = secs % (60 * 60);
        var minutes = Math.floor(divisor_for_minutes / 60);
        var divisor_for_seconds = divisor_for_minutes % 60;
        var seconds = Math.ceil(divisor_for_seconds);
        /*
         var obj = {
         "h": hours,
         "m": minutes,
         "s": seconds
         };
         */
        var obj = hours + ':' + minutes + ':' + seconds;
        return obj;
    };


    removeItem = function (array, item) {
        for (var i in array) {
            if (array[i] == item) {
                array.splice(i, 1);
                break;
            }
        }
    };

    dynamicSort = function (property) {
        var sortOrder = 1;
        if (property[0] === "-") {
            sortOrder = -1;
            property = property.substr(1, property.length - 1);
        }
        return function (a, b) {
            var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
            return result * sortOrder;
        }
    };

    dynamicSortMultiple = function () {
        /*
         .sort(dynamicSortMultiple("Name", "-Surname"));
         * save the arguments object as it will be overwritten
         * note that arguments object is an array-like object
         * consisting of the names of the properties to sort by
         */
        var props = arguments;
        return function (obj1, obj2) {
            var i = 0, result = 0, numberOfProperties = props.length;
            /* try getting a different result from 0 (equal)
             * as long as we have extra properties to compare
             */
            while (result === 0 && i < numberOfProperties) {
                result = dynamicSort(props[i])(obj1, obj2);
                i++;
            }
            return result;
        }
    };
    /*
     findInArray = function (arr, obj) {
     return (arr.indexOf(obj) != -1);
     };
     */

    parseVideoURL = function (url) {

        function getParm(url, base) {
            var re = new RegExp("(\\?|&)" + base + "\\=([^&]*)(&|$)");
            var matches = url.match(re);
            if (matches) {
                return(matches[2]);
            } else {
                return false;
            }
        }

        var retVal = {};
        var matches;
        var urlRegex = /(f|ht)tps?:\/\//g;
        retVal.urlType = url.match(urlRegex);
        if (url.match('(www.)?youtube|youtu\.be')) {
            retVal.provider = "youtube";
            retVal.id = getParm(url, "v");
            if(!retVal.id){
                var videoid = url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
                retVal.id = videoid[1];
            }

        } else if (matches = url.match(/vimeo.com\/(\d+)/)) {
            retVal.provider = "vimeo";
            retVal.id = matches[1];
        } else {
            return false;
        }
        return(retVal);
    }


    createUID = function () {
        // http://www.ietf.org/rfc/rfc4122.txt
        var s = [];
        var hexDigits = "0123456789abcdefghjkmnpqrst";
        for (var i = 0; i < 8; i++) {
            s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
        }
        return s.join("");
    };

    createUID2 = function () {
        // http://www.ietf.org/rfc/rfc4122.txt
        var s = [];
        var hexDigits = "0123456789abcdefghjkmnpqrst";
        for (var i = 0; i < 12; i++) {
            s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
        }
        return s.join("");
    };

    createUUID = function () {
        // http://www.ietf.org/rfc/rfc4122.txt
        var s = [];
        var hexDigits = "0123456789abcdef";
        for (var i = 0; i < 36; i++) {
            s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
        }
        s[14] = "4";  // bits 12-15 of the time_hi_and_version field to 0010
        s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1);  // bits 6-7 of the clock_seq_hi_and_reserved to 01
        s[8] = s[13] = s[18] = s[23] = "-";

        return s.join("");
    };

    var cancel = function (e) {
        if (e.preventDefault) e.preventDefault();
        return false;
    };

////////////////////////////////////////////
//  nested sortables dumped nested data
////////////////////////////////////////////
    dump = function (arr, level) {
        var dumped_text = "";
        if (!level) level = 0;

        //The padding given at the beginning of the line.
        var level_padding = "";
        for (var j = 0; j < level + 1; j++) level_padding += "    ";

        if (typeof(arr) == 'object') { //Array/Hashes/Objects
            for (var item in arr) {
                var value = arr[item];
                if (typeof(value) == 'object') { //If it is an array,
                    dumped_text += level_padding + "'" + item + "' ...\n";
                    dumped_text += dump(value, level + 1);
                } else {
                    dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
                }
            }
        } else { //Strings/Chars/Numbers etc.
            dumped_text = "===>" + arr + "<===(" + typeof(arr) + ")";
        }
        return dumped_text;
    };

    calculateFreeSpace = function (data, quota) {
        console.log(data);
        if (quota == 0) return false;
        var size = 0;
        for (var i = 0; i < data.length; i++) {
            console.log(data[i].error);
            if (!data[i].error) {
                size = size + data[i].size;
                console.log(size);
            }
        }
        ;

        return size; //quota > size ? true : false;
    }
//array functions
    /*
     array_diff = function (arr1) {
     // example 1: array_diff(['elements', 'of', 'mymedia'], ['mediabox', 'elements']);
     // returns 1: {0:'mediabox elements'}
     var retArr = {},
     argl = arguments.length,
     k1 = '',
     i = 1,
     k = '',
     arr = {};

     arr1keys: for (k1 in arr1) {
     for (i = 1; i < argl; i++) {
     arr = arguments[i];
     for (k in arr) {
     if (arr[k] === arr1[k1]) {
     // If it reaches here, it was found in at least one array, so try next value
     continue arr1keys;
     }
     }
     retArr[k1] = arr1[k1];
     }
     }

     return retArr;
     };
     */
    /*
     Array.prototype.removeFromArray = function(from, to) {
     var rest = this.slice((to || from) + 1 || this.length);
     this.length = from < 0 ? this.length + from : from;
     return this.push.apply(this, rest);
     };
     */
    /*
     getHoverScrollHeight = function (hsize) {
     if (hsize < 800) {
     scrollContainerHeight = 375;
     scrollContainerInnerHeight = 300;
     } else if (hsize > 800) {
     scrollContainerHeight = 455;
     scrollContainerInnerHeight = 380;
     }
     };

     setHoverScrollHeight = function () {
     scrollContainerHeight = viewportHeight < 800 ? 380 : 420;
     scrollContainerInnerHeight = viewportHeight < 800 ? 305 : 345;
     };

     disableKeyPress = function (e) {
     var key = window.event ? window.event.keyCode : e.which; //firefox
     return (key != 13);
     };
     */
    /*
     getElementsByClass = function (searchClass, node, tag) {
     var classElements = new Array();
     if (node == null)
     node = document;
     if (tag == null)
     tag = '*';
     var els = node.getElementsByTagName(tag);
     var elsLen = els.length;
     var pattern = new RegExp("(^|\\s)" + searchClass + "(\\s|$)");
     for (i = 0, j = 0; i < elsLen; i++) {
     if (pattern.test(els[i].className)) {
     classElements[j] = els[i];
     j++;
     }
     }
     return classElements;
     };

     $.fn.getNodeName = function () {
     // returns the nodeName of the first matched element, or ""
     return this[0] ? this[0].nodeName : "";
     };

     $.fn.serializeObject = function () {
     var o = {};
     var a = this.serializeArray();
     $.each(a, function () {
     if (o[this.name]) {
     if (!o[this.name].push) {
     o[this.name] = [o[this.name]];
     }
     o[this.name].push(this.value || '');
     } else {
     o[this.name] = this.value || '';
     }
     });
     return o;
     };

     $.fn.disable = function () {
     return this.each(function () {
     if (typeof this.disabled != "undefined") this.disabled = true;
     });
     };

     $.fn.enable = function () {
     return this.each(function () {
     if (typeof this.disabled != "undefined") this.disabled = false;
     });
     };


     $.fn.center = function () {
     this.css("position", "absolute");
     this.css("top", ( $(window).height() - this.height() ) / 2 + $(window).scrollTop() + "px");
     this.css("left", ( $(window).width() - this.width() ) / 2 + $(window).scrollLeft() + "px");
     return this;
     };

     $.fn.forceNumeric = function () {
     return this.each(function () {
     $(this).keydown(function (e) {
     var key = e.which || e.keyCode;
     if (!e.shiftKey && !e.altKey && !e.ctrlKey &&
     // numbers
     key >= 48 && key <= 57 ||
     // Numeric keypad
     key >= 96 && key <= 105 ||
     // comma, period and minus, . on keypad
     key == 190 || key == 188 || key == 109 || key == 110 ||
     // Backspace and Tab and Enter
     key == 8 || key == 9 || key == 13 ||
     // Home and End
     key == 35 || key == 36 ||
     // left and right arrows
     key == 37 || key == 39 ||
     // Del and Ins
     key == 46 || key == 45)
     return true;
     return false;
     });
     });
     };
     */
    /*
     prevent enter on input element
     usage: $(input).preventEnter()
     */
    $.fn.extend({
        preventEnter: function () {
            return this.each(function () {
                $(this).bind('keypress', function (e) {
                    var code = (e.keyCode ? e.keyCode : e.which);
                    if (code == 13) return false;
                });

            });
        }
    })
})(jQuery);
///////////////////////////////////////////////
/*
 resize image with js
 usage:
 img.onload = function() {
 var canvas = document.createElement("canvas");
 new thumbnailer(canvas, img, 188, 3); //this produces lanczos3
 //but feel free to raise it up to 8. Your client will appreciate
 //that the program makes full use of his machine.
 document.body.appendChild(canvas);
 returns a function that calculates lanczos weight
 }
 */


function lanczosCreate(lobes) {
    return function (x) {
        if (x > lobes)
            return 0;
        x *= Math.PI;
        if (Math.abs(x) < 1e-16)
            return 1
        var xx = x / lobes;
        return Math.sin(x) * Math.sin(xx) / x / xx;
    }
};

//elem: canvas element, img: image element, sx: scaled width, lobes: kernel radius
function thumbnailer(elem, img, sx, lobes) {
    this.canvas = elem;
    elem.width = img.width;
    elem.height = img.height;
    elem.style.display = "none";
    this.ctx = elem.getContext("2d");
    this.ctx.drawImage(img, 0, 0);
    this.img = img;
    this.src = this.ctx.getImageData(0, 0, img.width, img.height);
    this.dest = {
        width: sx,
        height: Math.round(img.height * sx / img.width)
    };
    this.dest.data = new Array(this.dest.width * this.dest.height * 3);
    this.lanczos = lanczosCreate(lobes);
    this.ratio = img.width / sx;
    this.rcp_ratio = 2 / this.ratio;
    this.range2 = Math.ceil(this.ratio * lobes / 2);
    this.cacheLanc = {};
    this.center = {};
    this.icenter = {};
    setTimeout(this.process1, 0, this, 0);
};

thumbnailer.prototype.process1 = function (self, u) {
    self.center.x = (u + 0.5) * self.ratio;
    self.icenter.x = Math.floor(self.center.x);
    for (var v = 0; v < self.dest.height; v++) {
        self.center.y = (v + 0.5) * self.ratio;
        self.icenter.y = Math.floor(self.center.y);
        var a, r, g, b;
        a = r = g = b = 0;
        for (var i = self.icenter.x - self.range2; i <= self.icenter.x + self.range2; i++) {
            if (i < 0 || i >= self.src.width)
                continue;
            var f_x = Math.floor(1000 * Math.abs(i - self.center.x));
            if (!self.cacheLanc[f_x])
                self.cacheLanc[f_x] = {};
            for (var j = self.icenter.y - self.range2; j <= self.icenter.y + self.range2; j++) {
                if (j < 0 || j >= self.src.height)
                    continue;
                var f_y = Math.floor(1000 * Math.abs(j - self.center.y));
                if (self.cacheLanc[f_x][f_y] == undefined)
                    self.cacheLanc[f_x][f_y] = self.lanczos(Math.sqrt(Math.pow(f_x * self.rcp_ratio, 2) + Math.pow(f_y * self.rcp_ratio, 2)) / 1000);
                weight = self.cacheLanc[f_x][f_y];
                if (weight > 0) {
                    var idx = (j * self.src.width + i) * 4;
                    a += weight;
                    r += weight * self.src.data[idx];
                    g += weight * self.src.data[idx + 1];
                    b += weight * self.src.data[idx + 2];
                }
            }
        }
        var idx = (v * self.dest.width + u) * 3;
        self.dest.data[idx] = r / a;
        self.dest.data[idx + 1] = g / a;
        self.dest.data[idx + 2] = b / a;
    }

    if (++u < self.dest.width)
        setTimeout(self.process1, 0, self, u);
    else
        setTimeout(self.process2, 0, self);
};

thumbnailer.prototype.process2 = function (self) {
    self.canvas.width = self.dest.width;
    self.canvas.height = self.dest.height;
    self.ctx.drawImage(self.img, 0, 0);
    self.src = self.ctx.getImageData(0, 0, self.dest.width, self.dest.height);
    var idx, idx2;
    for (var i = 0; i < self.dest.width; i++) {
        for (var j = 0; j < self.dest.height; j++) {
            idx = (j * self.dest.width + i) * 3;
            idx2 = (j * self.dest.width + i) * 4;
            self.src.data[idx2] = self.dest.data[idx];
            self.src.data[idx2 + 1] = self.dest.data[idx + 1];
            self.src.data[idx2 + 2] = self.dest.data[idx + 2];
        }
    }
    self.ctx.putImageData(self.src, 0, 0);
    self.canvas.style.display = "block";
};

function resizeCrop( src, width, height,extraX ){
    var crop = width == 0 || height == 0;
    // not resize
    if(src.width && (width && height == 0)){
        height = src.height * (width / src.width);
    }

    // check scale
    var xscale = width  / src.width;
    var yscale = height / src.height;

    var scale  = crop ? Math.min(xscale, yscale) : Math.max(xscale, yscale);
    // create empty canvas
    var canvas = document.createElement("canvas");
    canvas.width  = width ? width   : Math.round(src.width  * scale);
    canvas.height = height ? height : Math.round(src.height * scale);
    canvas.getContext("2d").scale(scale,scale);
    //alert('orig: '+scale+ ' osztva: '+((src.width * scale) - canvas.width))
    //crop it top center
    canvas.getContext("2d").drawImage(src, ((src.width * scale) - canvas.width) * -.5 , ((src.height * scale) - canvas.height) * -.5 );
    return canvas;
}
///////////////////////////////////////////////
function isSupported() {
    return localStorage ? true : false;
};


///////////////////////////////////////////////
if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (obj, fromIndex) {
        if (fromIndex == null) {
            fromIndex = 0;
        } else if (fromIndex < 0) {
            fromIndex = Math.max(0, this.length + fromIndex);
        }
        for (var i = fromIndex, j = this.length; i < j; i++) {
            if (this[i] === obj)
                return i;
        }
        return -1;
    };
}
;

function toObject(arr) {
    var rv = {};
    for (var i = 0; i < arr.length; ++i)
        rv[i] = arr[i];
    return rv;
}

function clearNULL(x) {
    return x == null ? '' : x;
}

/////////////////////////////////////
var _eventHandlers = {};
//register/unregister attached events to nodes
function addEventO(node, event, handler, capture, _eventHandlers) {
    if (node == '')
        return false;
    if (!(node in _eventHandlers))
        _eventHandlers[node] = {};
    if (!(event in _eventHandlers[node]))
        _eventHandlers[node][event] = [];
    _eventHandlers[node][event].push([node, handler, capture]);
    if (node.addEventListener)
        node.addEventListener(event, handler, capture);
    else
        node.attachEvent('on' + event, handler, capture);
};

function removeAllEventsByNode(node, event, _eventHandlers) {
    if (node in _eventHandlers) {
        var handlers = _eventHandlers[node];
        if (event in handlers) {
            var eventHandlers = handlers[event];
            for (var i = eventHandlers.length; i--;) {
                var handler = eventHandlers[i];
                if (node.removeEventListener)
                    node.removeEventListener(event, handler[0], handler[1]);
                else
                    node.detachEvent('on' + event, handler[0], handler[1]);
            }
        }
    }
};

function removeAllEventsByEvent(event, _eventHandlers) {
    for (node in _eventHandlers) {
        var handlers = _eventHandlers[node];
        for (event in handlers) {
            var eventHandlers = handlers[event];
            for (var i = eventHandlers.length; i--;) {
                var handler = eventHandlers[i];
                if (handler[0].removeEventListener)
                    handler[0].removeEventListener(event, handler[1], handler[2]);
                else
                    handler[0].detachEvent('on' + event, handler[1], handler[2]);
            }
        }
    }
};

/*
 var pos = getElementAbsolutePos(myElement);
 window.alert("Element's left: " + pos.x + " and top: " + pos.y);
 */
function __getIEVersion() {
    var rv = -1; // Return value assumes failure.
    if (navigator.appName == 'Microsoft Internet Explorer') {
        var ua = navigator.userAgent;
        var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null)
            rv = parseFloat(RegExp.$1);
    }
    return rv;
};

function __getOperaVersion() {
    var rv = 0; // Default value
    if (window.opera) {
        var sver = window.opera.version();
        rv = parseFloat(sver);
    }
    return rv;
};

var __userAgent = navigator.userAgent;
var __isIE = navigator.appVersion.match(/MSIE/) != null;
var __IEVersion = __getIEVersion();
var __isIENew = __isIE && __IEVersion >= 8;
var __isIEOld = __isIE && !__isIENew;

var __isFireFox = __userAgent.match(/firefox/i) != null;
var __isFireFoxOld = __isFireFox && ((__userAgent.match(/firefox\/2./i) != null) ||
    (__userAgent.match(/firefox\/1./i) != null));
var __isFireFoxNew = __isFireFox && !__isFireFoxOld;

var __isWebKit = navigator.appVersion.match(/WebKit/) != null;
var __isChrome = navigator.appVersion.match(/Chrome/) != null;
var __isOpera = window.opera != null;
var __operaVersion = __getOperaVersion();
var __isOperaOld = __isOpera && (__operaVersion < 10);
/*
 function __parseBorderWidth(width) {
 var res = 0;
 if (typeof(width) == "string" && width != null && width != "") {
 var p = width.indexOf("px");
 if (p >= 0) {
 res = parseInt(width.substring(0, p));
 }
 else {
 //do not know how to calculate other values
 //(such as 0.5em or 0.1cm) correctly now
 //so just set the width to 1 pixel
 res = 1;
 }
 }
 return res;
 };

 //returns border width for some element
 function __getBorderWidth(element) {
 var res = new Object();
 res.left = 0;
 res.top = 0;
 res.right = 0;
 res.bottom = 0;
 if (window.getComputedStyle) {
 //for Firefox
 var elStyle = window.getComputedStyle(element, null);
 res.left = parseInt(elStyle.borderLeftWidth.slice(0, -2));
 res.top = parseInt(elStyle.borderTopWidth.slice(0, -2));
 res.right = parseInt(elStyle.borderRightWidth.slice(0, -2));
 res.bottom = parseInt(elStyle.borderBottomWidth.slice(0, -2));
 }
 else {
 //for other browsers
 res.left = __parseBorderWidth(element.style.borderLeftWidth);
 res.top = __parseBorderWidth(element.style.borderTopWidth);
 res.right = __parseBorderWidth(element.style.borderRightWidth);
 res.bottom = __parseBorderWidth(element.style.borderBottomWidth);
 }

 return res;
 };

 //returns the absolute position of some element within document
 function getElementAbsolutePos(element) {
 var res = new Object();
 res.x = 0;
 res.y = 0;
 if (element !== null) {
 if (element.getBoundingClientRect) {
 var viewportElement = document.documentElement;
 var box = element.getBoundingClientRect();
 var scrollLeft = viewportElement.scrollLeft;
 var scrollTop = viewportElement.scrollTop;

 res.x = box.left + scrollLeft;
 res.y = box.top + scrollTop;

 }
 else { //for old browsers
 res.x = element.offsetLeft;
 res.y = element.offsetTop;

 var parentNode = element.parentNode;
 var borderWidth = null;

 while (offsetParent != null) {
 res.x += offsetParent.offsetLeft;
 res.y += offsetParent.offsetTop;

 var parentTagName =
 offsetParent.tagName.toLowerCase();

 if ((__isIEOld && parentTagName != "table") ||
 ((__isFireFoxNew || __isChrome) &&
 parentTagName == "td")) {
 borderWidth = kGetBorderWidth
 (offsetParent);
 res.x += borderWidth.left;
 res.y += borderWidth.top;
 }

 if (offsetParent != document.body &&
 offsetParent != document.documentElement) {
 res.x -= offsetParent.scrollLeft;
 res.y -= offsetParent.scrollTop;
 }


 //next lines are necessary to fix the problem
 //with offsetParent
 if (!__isIE && !__isOperaOld || __isIENew) {
 while (offsetParent != parentNode &&
 parentNode !== null) {
 res.x -= parentNode.scrollLeft;
 res.y -= parentNode.scrollTop;
 if (__isFireFoxOld || __isWebKit) {
 borderWidth =
 kGetBorderWidth(parentNode);
 res.x += borderWidth.left;
 res.y += borderWidth.top;
 }
 parentNode = parentNode.parentNode;
 }
 }

 parentNode = offsetParent.parentNode;
 offsetParent = offsetParent.offsetParent;
 }
 }
 }
 return res;
 };
 */
/*
 function hasClass(ele, cls) {
 return ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
 };

 function addClass(ele, cls) {
 if (!this.hasClass(ele, cls))
 ele.className += " " + cls;
 };

 function removeClass(ele, cls) {
 if (hasClass(ele, cls)) {
 var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
 ele.className = ele.className.replace(reg, ' ');
 }
 };

 function replaceClass(ele, oldClass, newClass) {
 if (hasClass(ele, oldClass)) {
 removeClass(ele, oldClass);
 addClass(ele, newClass);
 }
 return;
 };

 function toggleClass(ele, cls1, cls2) {
 if (hasClass(ele, cls1)) {
 replaceClass(ele, cls1, cls2);
 } else if (hasClass(ele, cls2)) {
 replaceClass(ele, cls2, cls1);
 } else {
 addClass(ele, cls1);
 }
 };
 */
var util = {

    // findPos() by quirksmode.org
    // Finds the absolute position of an element on a page
    findPos: function (obj) {
        var curleft = curtop = 0;
        if (obj.offsetParent) {
            do {
                curleft += obj.offsetLeft;
                curtop += obj.offsetTop;
            } while (obj = obj.offsetParent);
        }
        return [curleft, curtop];
    },

    // getPageScroll() by quirksmode.org
    // Finds the scroll position of a page
    getPageScroll: function () {
        var xScroll, yScroll;
        if (self.pageYOffset) {
            yScroll = self.pageYOffset;
            xScroll = self.pageXOffset;
        } else if (document.documentElement && document.documentElement.scrollTop) {
            yScroll = document.documentElement.scrollTop;
            xScroll = document.documentElement.scrollLeft;
        } else if (document.body) {// all other Explorers
            yScroll = document.body.scrollTop;
            xScroll = document.body.scrollLeft;
        }
        return [xScroll, yScroll]
    },

    // Finds the position of an element relative to the viewport.
    findPosRelativeToViewport: function (obj) {
        var objPos = this.findPos(obj)
        var scroll = this.getPageScroll()
        return [ objPos[0] - scroll[0], objPos[1] - scroll[1] ]
    }

};

/*
 on every page init affix elements
 */
function initAffix (affixElements) {
    for ( var key in affixElements ) {
        $( affixElements[key] ).affix( {
            offset: {
                top   : function () {
                    return $window.width() <= 1200 ? 220 : 220
                },
                bottom: -100
            }
        } );
    }
}
/*
 every page scroll to working area topline
 */
function scrollToWorkingArea (object){
    $('html, body').animate({
        scrollTop: object.offset().top
    }, 1000);
}

/////////////////////////////////////
//calculate LI elements in a row to determine the position
//to insert detail LI
function calculateLIsInRow(container) {
    "use strict";
    if($('#detailRow' ).length) $('#detailRow' ).remove();
    var returnArray;
    var lisInLastRow;
    var lisInRow = 0,
        $lis = $(container).find('li.rootClass:not(.hiddenClass)');
    $lis.each(function () {
        if ($(this).prev().length > 0) {
            if ($(this).position().top != $(this).prev().position().top) return false;
            lisInRow++;
        } else {
            lisInRow++;
        }
    });

    lisInLastRow = $lis.length % lisInRow;
    if (lisInLastRow == 0) lisInLastRow = lisInRow;
    returnArray = [ parseInt($lis.length / lisInRow) + 1, lisInRow, lisInLastRow, $lis.length];
    return returnArray;
    //alert('No: of lis in a row = ' + lisInRow + '<br>' + 'No: of lis in last row = ' + lisInLastRow);
}

/////////////////////////////////////
//insert detail row into list
//to insert detail LI
function createDetailRow(parent, row, rowData, pointerleft, containerHeight) {
    "use strict";
    // create container
    if ($('#detailRow').length == 1 ) {
        $('#detailRow').find('.pointer-top').css('left', pointerleft);
        $('#detailRow').find('.inner').html('');
        $('#detailRow').attr('data-detail-id',parent.attr('id'));
    } else {
        $('#detailRow').remove();
        var parentClass = 'span8';
        if (parent.parent().attr('class').match(/span(\d+)/)[0] != null)
            parentClass = parent.parent().attr('class').match(/span(\d+)/)[0];
        $('html, body').animate({
            scrollTop: parent.offset().top
        }, 1000);

        parent
            .parent()
            .children('li.rootClass:eq(' + ( rowData[0] - row == 0 ? rowData[3] - 1 : row * rowData[1] - 1 ) + ')')
            .after('<li id="detailRow" data-detail-id="' + parent.attr('id') + '" data-row="' + row + '" class="' + parentClass + '" style="background:#cbcbcb;"><div class="pointer-top" style="left:' + pointerleft + '"></div><span class="icon-remove"></span><div id="content" class="closed transitions "><div class="inner"></div></div></li>');
        /*
         console.log(parent
         .parent()
         .children('li:eq(' + ( rowData[0] - row == 0 ? rowData[3] - 1 : row * rowData[1] - 1 ) + ')').attr('id'))
         console.log('row: '+row)
         console.log('outerHeight')
         */
        // open/close container
        var content = $('#detailRow > #content');
        content.css('height', containerHeight ? containerHeight : '200px');
        content.inner = $('#detailRow > #content .inner');

        content.toggleClass('open closed');
        content.contentHeight = content.outerHeight();
        if (content.hasClass('closed')) {
            content.removeClass('transitions').css('max-height', content.contentHeight);
            setTimeout(function () {
                content.addClass('transitions').css({'max-height': 0, 'opacity': 0});
            }, 10);
        } else if (content.hasClass('open')) {
            content.contentHeight += content.inner.outerHeight();
            content.css({'max-height': content.contentHeight, 'opacity': 1});
        }

        $('span.icon-remove').bind("click", function () {
            content.toggleClass('open closed');
            content.contentHeight = containerHeight;//content.outerHeight();
            if (content.hasClass('closed')) {
                content.removeClass('transitions').css('max-height', content.contentHeight);
                setTimeout(function () {
                    content.addClass('transitions').css({'max-height': 0, 'opacity': 0});
                }, 10);
            }
            /*else if (content.hasClass('open')){
             content.contentHeight += content.inner.outerHeight();
             content.css({'max-height': content.contentHeight,'opacity': 1});
             console.log('papo2')
             }*/
            content.animate({height: 0}, 500, function () {
                $('#detailRow').remove();
            })

        });
    }
}

function cancelFullscreen(el) {
    var requestMethod = el.cancelFullScreen || el.webkitCancelFullScreen || el.mozCancelFullScreen || el.exitFullscreen;
    if (requestMethod) { // cancel full screen.
        requestMethod.call(el);
    } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
        /*
         var wscript = new ActiveXObject("WScript.Shell");
         if (wscript !== null) {
         wscript.SendKeys("{F11}");
         }
         */
    }
}

function requestFullscreen(element) {
    // Supports most browsers and their versions.
    var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullScreen;
//alert(requestMethod)
    if (requestMethod) { // Native full screen.
        requestMethod.call(element);
        $('#gotoFullscreen').removeClass('hiddenClass').attr('data-type', 'screen').text('Exit fullscreen');
    } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
        $('#gotoFullscreen').addClass('hiddenClass');
        /*
         var wscript = new ActiveXObject("WScript.Shell");
         if (wscript !== null) {
         wscript.SendKeys("{F11}");
         } else {
         alert('Csak manuálisan válthat teljes képernyős üzemmódra');
         }
         */
    }
}

function toggleFullscreen() {
    var elem = document.body; // Make the body go full screen.
    var isInFullScreen = (document.fullScreenElement && document.fullScreenElement !== null) || (document.mozFullScreen || document.webkitIsFullScreen);
    console.log('inFull? '+ isInFullScreen)
    if (isInFullScreen) {
        cancelFullscreen(document);
        $('#gotoFullscreen').attr('data-type', 'fullscreen').text('Fullscreen');
    } else {
        requestFullscreen(elem);
        $('#gotoFullscreen').removeClass('hiddenClass').attr('data-type', 'screen').text('Exit fullscreen');
    }

    return false;
}
/*
 @param:  settings -> array (url, responseType, type )
 @return  json or html / if json, sendMessage function is called
 */
function getJsonData(settings) {
    var options = jQuery.extend({
        url: '',
        responseType: 'json',
        async: true,
        data: {},
        type: 'POST',
        onOpen: function () {
        },
        onClose: function () {
        },
        onSelect: function () {
        }
    }, settings);

    var response = $.ajax({
        url: options.url,
        data: options.data,
        type: options.type,
        dataType: options.responseType,
        async: false
    }).responseText;

    if (options.responseType == 'json') {
        response = $.parseJSON(response);
        response.type ? sendMessage('alert-' + response.type, response.message) : '';
        return response.result ? response : (response.type == 'success' ? true : false);
    }

    return response;
}

function TOlog(message, a) {
    if (_globallogging && window.console && window.console.log) {
        window.console.log(message);
        window.console.log(a);
    }
}