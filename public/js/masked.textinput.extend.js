$.fn.extend({
    metadata: function() {
        var strCollectionKey = "__metadata__";
        var objData = null;
        var strKey = "";
        var strValue = "";
        // Check to see how many arguments were passed-in. The
        // number of arguments passed-in will determine the
        // type of action to take:
        // 0 :: Get entire data collection (first element).
        // 1 :: Get data point (first element).
        // 2 :: Set data point (entire collection).
        if (arguments.length == 0) {
            // Return the data collection for the first element
            // in the jQuery collection.
            // Get the meta data collection. If this value has
            // not been set yet, it will return NULL.
            objData = this.data(strCollectionKey);
            // Check to see if we have a data object yet.
            if (!objData) {
                // The data store has been set yet. We want to
                // set a value (even through we are going to
                // return it) so that we know that we will
                // always be referring to the same struct (by
                // reference) in later calls.
                this.data(strCollectionKey, {});
            }
            // ASSERT: At this point, we know that a strcuture
            // exists in our meta data key (even if it is empty).
            // Return an meta data collection.
            return this.data(strCollectionKey);
        } else if (arguments.length == 1) {
            // Return the value for the first element in the
            // jQuery collection. To get the data collection, we
            // will call the metaData() method recursively to
            // get our store before we return the data point.
            return this.metaData()[arguments[0]];
        } else if (arguments.length == 2) {
            // Set the given name value. We will be doing this
            // for each element in our jQuery collection.
            // Create a local reference to the arguments (so that
            // we can later refer to them in the each() method.
            strKey = arguments[0];
            strValue = arguments[1];
            // Iterate over each element in the collection and
            // update the data.
            this.each(function(intI, objElement) {
                // Get a jquery reference to the element in
                // our collection iteration.
                var jElement = jQuery(objElement);
                // Get the meta data collection. To get this,
                // we will call the metaData() method recursively
                // to get the store.
                var objData = jElement.metaData();
                // Set the value. Because the data store is
                // passed by reference (why I made a point to
                // set it before returning it above), we don't
                // need to re-store it.
                objData[strKey] = strValue;
            });
            // Return the current jQuery object for method
            // chaining capabilities.
            return this;
        }
    },

    setMask: function() {
        $list = this.toArray();

        for (var i in $list) {
            var self = this[i];
            $metadata = $(self).metadata();
            if (!$metadata) continue;

            v = $metadata._mask;
            p = $metadata.placeholder;

            if (v == undefined) v = $(self).attr("_mask");
            if (p == undefined) p = $(self).attr("_placeholder");

            s = null;
            if (p != undefined) s = { placeholder: p };

            if (v != undefined) $(self).mask(v, s);
        }
    }
});
