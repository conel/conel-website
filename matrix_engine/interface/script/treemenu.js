/**
* treemenu class
*/
function treemenu(iconpath, myname, linkTarget, defaultClass, usePersistence, noTopLevelImages) {
		// Properties
		this.iconpath         = iconpath;
		this.myname           = myname;
		this.linkTarget       = linkTarget;
		this.defaultClass     = defaultClass;
		this.usePersistence   = usePersistence;
		this.noTopLevelImages = noTopLevelImages;
		this.n                = new Array();
	
		this.nodeRefs       = new Array();
		this.branches       = new Array();
		this.branchStatus   = new Array();
		this.layerRelations = new Array();
		this.childParents   = new Array();
		this.cookieStatuses = new Array();

		this.preloadImages();
	}

	/**
	* Adds a node to the tree
	*/
	treemenu.prototype.addItem = function (newNode) {
		newIndex = this.n.length;
		this.n[newIndex] = newNode;
		
		return this.n[newIndex];
	}

/**
* Preload images hack for Mozilla
*/
	treemenu.prototype.preloadImages = function ()
	{
		var plus       = new Image; plus.src       = this.iconpath + '/menu_corner_plus.gif';
		var minus       = new Image; minus.src       = this.iconpath + '/menu_corner_minus.gif';
		var branch       = new Image; branch.src       = this.iconpath + '/blank.gif';
	
		var linebottom = new Image; linebottom.src = this.iconpath + '/linebottom.gif';
		var line       = new Image; line.src       = this.iconpath + '/line.gif';
	}

	/**
	* Main function that draws the menu and assigns it
	* to the layer (or document.write()s it)
	*/
	treemenu.prototype.drawMenu = function ()// OPTIONAL ARGS: nodes = [], level = [], prepend = '', expanded = false, visbility = 'inline', parentLayerID = null
	{
		/**
	    * Necessary variables
	    */
		var output        = '';
		var modifier      = '';
		var layerID       = '';
		var parentLayerID = '';
	
		/**
	    * Parse any optional arguments
	    */
		var nodes         = arguments[0] ? arguments[0] : this.n
		var level         = arguments[1] ? arguments[1] : [];
		var prepend       = arguments[2] ? arguments[2] : '';
		var expanded      = arguments[3] ? arguments[3] : false;
		var visibility    = arguments[4] ? arguments[4] : 'inline';
		var parentLayerID = arguments[5] ? arguments[5] : null;

		var currentlevel  = level.length;
	
		for (var i=0; i<nodes.length; i++) {
		
			level[currentlevel] = i+1;
			layerID = this.myname + '_' + 'node_' + this.implode('_', level);
	
			/**
            * Store this object in the nodeRefs array
            */
			this.nodeRefs[layerID] = nodes[i];

			/**
	        * Store the child/parent relationship
	        */
			this.childParents[layerID] = parentLayerID;
	
			/**
	        * Single root branch is always expanded
	        */
			if ((parentLayerID == null && (nodes.length == 1 || this.noTopLevelImages))) {
				expanded = true;
	
			} else if (nodes[i].expanded) {
				expanded = true;
	
			} else {
				expanded = false;
			}
	
			/**
	        * Make sure visibility is correct based on parent status
	        */
			visibility =  this.checkParentVisibility(layerID) ? visibility : 'none';
	
			/**
	        * Setup branch status and build an indexed array
			* of branch layer ids
	        */
			if (nodes[i].n.length > 0) {
				this.branchStatus[layerID] = expanded;
				this.branches[this.branches.length] = layerID;
			}
	
			/**
	        * Setup toggle relationship
	        */
			if (!this.layerRelations[parentLayerID]) {
				this.layerRelations[parentLayerID] = new Array();
			}
			this.layerRelations[parentLayerID][this.layerRelations[parentLayerID].length] = layerID;
	
			/**
	        * Branch images
	        */
			var gifname = nodes[i].n.length && nodes[i].isDynamic ? (expanded ? 'menu_corner_minus' : 'menu_corner_plus') : 'blank';
			if(nodes[i].iconlink) {
				var iconimg = nodes[i].icon ? this.stringFormat('<a href="#" onClick="{0}"><img src="{1}/{2}" width="20" height="17" align="top" id="icon_{3}" border="0"></a>', nodes[i].iconlink,this.iconpath, nodes[i].icon, layerID) : '';
			} else {
				var iconimg = nodes[i].icon ? this.stringFormat('<img src="{0}/{1}" width="20" height="17" align="top" id="icon_{2}" border="0">', this.iconpath, nodes[i].icon, layerID) : '';
			}				
			/**
			* Add event handlers
			*/
			var eventHandlers = "";
			for (j in nodes[i].events) {
				eventHandlers += this.stringFormat('{0}="{1}" ', j, nodes[i].events[j]);
			}

			/** 
	        * Build the html to write to the document
			* IMPORTANT:
			* document.write()ing the string: '<div style="display:...' will screw up nn4.x
	        */
			var layerTag  = this.stringFormat('<div id="{0}" style="display: {1} " class="{2}">', layerID, visibility, (nodes[i].cssClass ? nodes[i].cssClass : this.defaultClass));
			var onMDown   = nodes[i].n.length  && nodes[i].isDynamic ? this.stringFormat('onmousedown="{0}.toggleBranch(\'{1}\', true)" style="cursor: pointer; cursor: hand"', this.myname, layerID) : '';
			var imgTag    = this.stringFormat('<img src="{0}/{1}{2}.gif" width="20" height="17" align="top" border="0" name="img_{3}" {4}>', this.iconpath, gifname, modifier, layerID, onMDown);
			var linkTarget= nodes[i].linkTarget ? nodes[i].linkTarget : this.linkTarget;
			var linkStart = nodes[i].link ? this.stringFormat('<a href="{0}" target="{1}">', nodes[i].link, linkTarget) : '';
			var linkEnd   = nodes[i].link ? '</a>' : '';

			output = this.stringFormat('{0}<nobr>{1}{2}{3}{4}<span {5}>{6}</span>{7}</nobr><br><img src="'+this.iconpath+'/menu_seperator.gif" width="100%" height="2" align="top" border="0"></div>',
			                  layerTag,
							  prepend,
			                  parentLayerID == null && (nodes.length == 1 || this.noTopLevelImages) ? '' : imgTag,
							  iconimg,
							  linkStart,
							  eventHandlers,
							  nodes[i].title,
							  linkEnd);
//<td bgcolor="#EDEDED" valign="top" align="left" width="12"><img src="menu_seperator"></td>
	
			/**
	        * Write out the HTML. Uses document.write for speed over layers and
			* innerHTML. This however means no dynamic adding/removing nodes on
			* the client side. This could be conditional I guess if dynamic
			* adding/removing is required.
	        */
			document.write(output + "\r\n");

			/**
	        * Traverse sub nodes ?
	        */
			if (nodes[i].n.length) {
				/**
	            * Determine what to prepend. If there is only one root
				* node then the prepend to pass to children is nothing.
				* Otherwise it depends on where we are in the tree.
	            */
				if (parentLayerID == null && (nodes.length == 1 || this.noTopLevelImages)) {
					var newPrepend = '';
	
				} else {
					var newPrepend = prepend + this.stringFormat('<img src="{0}/blank.gif" width="20" height="17" align="top">', this.iconpath);
				}
				this.drawMenu(nodes[i].n,
				              level,
				              newPrepend,
				              nodes[i].expanded,
				              expanded ? 'inline' : 'none',
				              layerID);
			}
		}
	}

	/**
	* Toggles a branches visible status. Called from resetBranches()
	* and also when a +/- graphic is clicked.
	*/
	// OPTIONAL ARGS: fireEvents = true
	treemenu.prototype.toggleBranch = function (layerID, updateStatus)  {
		var currentDisplay = this.getLayer(layerID).style.display;
		var newDisplay     = (this.branchStatus[layerID] && currentDisplay == 'inline') ? 'none' : 'inline';
		var fireEvents     = arguments[2] != null ? arguments[2] : true;
	
		for (var i=0; i<this.layerRelations[layerID].length; i++) {
	
			if (this.branchStatus[this.layerRelations[layerID][i]]) {
				this.toggleBranch(this.layerRelations[layerID][i], false);
			}
	
			this.getLayer(this.layerRelations[layerID][i]).style.display = newDisplay;
		}
	
		if (updateStatus) {
			this.branchStatus[layerID] = !this.branchStatus[layerID];
	
			/**
	        * Persistence
	        */
			if (!arguments[2] && this.usePersistence) {
				this.setExpandedStatusForCookie(layerID, this.branchStatus[layerID]);
			}

			/**
			* Fire custom events
			*/
			if (fireEvents) {
				nodeObject = this.nodeRefs[layerID];
	
				if (nodeObject.ontoggle != null) {
					eval(nodeObject.ontoggle);
				}
				
				if (newDisplay == 'none' && nodeObject.oncollapse != null) {
					eval(nodeObject.oncollapse);
				} else if (newDisplay == 'inline' && nodeObject.onexpand != null){
					eval(nodeObject.onexpand);
				}
			}

			// Swap image
			this.swapImage(layerID);
		}

		// Swap icon
		this.swapIcon(layerID);
	}

	/**
	* Swaps the plus/minus branch images
	*/
	treemenu.prototype.swapImage = function (layerID)
	{
		var imgSrc = document.images['img_' + layerID].src;
	
		var re = /^(.*)(menu_corner_plus|menu_corner_minus)(bottom|top|single)?.gif$/
		if (matches = imgSrc.match(re)) {
	
			document.images['img_' + layerID].src = this.stringFormat('{0}{1}{2}{3}',
			                                                matches[1],
															matches[2] == 'menu_corner_plus' ? 'menu_corner_minus' : 'menu_corner_plus',
															matches[3] ? matches[3] : '',
															'.gif');
		}
	}

	/**
	* Swaps the icon for the expanded icon if one
	* has been supplied.
	*/
	treemenu.prototype.swapIcon = function (layerID) {
		if (document.images['icon_' + layerID]) {
			var imgSrc = document.images['icon_' + layerID].src;
	
			if (this.nodeRefs[layerID].icon && this.nodeRefs[layerID].expandedIcon) {
				var newSrc = (imgSrc.indexOf(this.nodeRefs[layerID].expandedIcon) == -1 ? this.nodeRefs[layerID].expandedIcon : this.nodeRefs[layerID].icon);
	
				document.images['icon_' + layerID].src = this.iconpath + '/' + newSrc;
			}
		}
	}


	/**
	* Returns the appropriate layer accessor
	*/
	treemenu.prototype.getLayer = function (layerID) {
		if (is_ie4) {
			return document.all(layerID);
	
		} else if (document.getElementById(layerID)) {
			return document.getElementById(layerID);
	
		} else if (document.all(layerID)) {
			return document.all(layerID);
		}
	}

	/**	
	* Save the status of the layer
	*/
	treemenu.prototype.setExpandedStatusForCookie = function (layerID, expanded) {
		this.cookieStatuses[layerID] = expanded;
		this.saveCookie();
	}

/**
* Load the status of the layer
*/
	treemenu.prototype.getExpandedStatusFromCookie = function (layerID)
	{
		if (this.cookieStatuses[layerID]) {
			return this.cookieStatuses[layerID];
		}

		return false;
	}

/**
* Saves the cookie that holds which branches are expanded.
* Only saves the details of the branches which are expanded.
*/
	treemenu.prototype.saveCookie = function ()
	{
		var cookieString = new Array();

		for (var i in this.cookieStatuses) {
			if (this.cookieStatuses[i] == true) {
				cookieString[cookieString.length] = i;
			}
		}
		
		document.cookie = 'treemenuBranchStatus=' + cookieString.join(':');
	}

/**
* Reads cookie parses it for status info and
* stores that info in the class member.
*/
	treemenu.prototype.loadCookie = function ()
	{
		var cookie = document.cookie.split('; ');

		for (var i=0; i < cookie.length; i++) {
			var crumb = cookie[i].split('=');
			if ('treemenuBranchStatus' == crumb[0] && crumb[1]) {
				var expandedBranches = crumb[1].split(':');
				for (var j=0; j<expandedBranches.length; j++) {
					this.cookieStatuses[expandedBranches[j]] = true;
				}
			}
		}
	}

/**
* Reset branch status
*/
	treemenu.prototype.resetBranches = function ()
	{

		this.loadCookie();

		for (var i=0; i<this.branches.length; i++) {
			var status = this.getExpandedStatusFromCookie(this.branches[i]);
			// Only update if it's supposed to be expanded and it's not already
			if (status == true && this.branchStatus[this.branches[i]] != true) {
				if (this.checkParentVisibility(this.branches[i])) {
					this.toggleBranch(this.branches[i], true, false);
				} else {
					this.branchStatus[this.branches[i]] = true;
					this.swapImage(this.branches[i]);
				}
			}
		}
	}

/**
* Checks whether a branch should be open
* or not based on its parents' status
*/
	treemenu.prototype.checkParentVisibility = function (layerID)
	{
		if (this.in_array(this.childParents[layerID], this.branches)
		    && this.branchStatus[this.childParents[layerID]]
			&& this.checkParentVisibility(this.childParents[layerID]) ) {
			
			return true;
	
		} else if (this.childParents[layerID] == null) {
			return true;
		}
		
		return false;
	}

/**
* New C# style string formatter
*/
	treemenu.prototype.stringFormat = function (strInput)
	{
		var idx = 0;
	
		for (var i=1; i<arguments.length; i++) {
			while ((idx = strInput.indexOf('{' + (i - 1) + '}', idx)) != -1) {
				strInput = strInput.substring(0, idx) + arguments[i] + strInput.substr(idx + 3);
			}
		}
		
		return strInput;
	}

/**
* Also much adored, the PHP implode() function
*/
	treemenu.prototype.implode = function (seperator, input)
	{
		var output = '';
	
		for (var i=0; i<input.length; i++) {
			if (i == 0) {
				output += input[i];
			} else {
				output += seperator + input[i];
			}
		}
		
		return output;
	}

	/**
	* Aah, all the old favourites are coming out...
	*/
	treemenu.prototype.in_array = function (item, arr) {
		for (var i=0; i<arr.length; i++) {
			if (arr[i] == item) {
				return true;
			}
		}
	
		return false;
	}

	/**
	* TreeNode Class
	*/
	function TreeNode(title, icon, link, iconlink, expanded, isDynamic, cssClass, linkTarget, expandedIcon) {
		this.title        = title;
		this.icon         = icon;
		this.expandedIcon = expandedIcon;
		this.link         = link;
		this.iconlink	  = iconlink;
		this.expanded     = expanded;
		this.isDynamic    = isDynamic;
		this.cssClass     = cssClass;
		this.linkTarget   = linkTarget;
		this.n            = new Array();
		this.events       = new Array();
		this.handlers     = null;
		this.oncollapse   = null;
		this.onexpand     = null;
		this.ontoggle     = null;
	}

	/**
	* Adds a node to an already existing node
	*/
	TreeNode.prototype.addItem = function (newNode) {
		newIndex = this.n.length;
		this.n[newIndex] = newNode;
		
		return this.n[newIndex];
	}

	/**
	* Sets an event for this particular node
	*/
	TreeNode.prototype.setEvent = function (eventName, eventHandler) {
		switch (eventName.toLowerCase()) {
			case 'onexpand':
				this.onexpand = eventHandler;
				break;

			case 'oncollapse':
				this.oncollapse = eventHandler;
				break;

			case 'ontoggle':
				this.ontoggle = eventHandler;
				break;

			default:
				this.events[eventName] = eventHandler;
		}
	}

/**
* That's the end of the tree classes. What follows is
* the browser detection code.
* Severly curtailed all this as only certain elements
* are required by treemenu, specifically:
*  o is_ie4up
*  o is_nav6up
*  o is_gecko
*/

    // convert all characters to lowercase to simplify testing
    var agt=navigator.userAgent.toLowerCase();

    // *** BROWSER VERSION ***
    // Note: On IE5, these return 4, so use is_ie5up to detect IE5.
    var is_major = parseInt(navigator.appVersion);
    var is_minor = parseFloat(navigator.appVersion);

    var is_ie     = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
    var is_ie4    = (is_ie && (is_major == 4) && (agt.indexOf("msie 4")!=-1) );
