var currentTallest = 0,
	currentRowStart = 0,
	rowDivs = new Array();

function setConformingHeight(b, a) {
	b.data("originalHeight", (b.data("originalHeight") == undefined) ? (b.height()) : (b.data("originalHeight")));
	b.height(a)
}

function getOriginalHeight(a) {
	return (a.data("originalHeight") == undefined) ? (a.height()) : (a.data("originalHeight"))
}

function columnConform(a) {
	$(a).each(function() {
		var c = $(this);
		var b = c.position().top;
		if (currentRowStart != b) {
			for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
				setConformingHeight(rowDivs[currentDiv], currentTallest)
			}
			rowDivs.length = 0;
			currentRowStart = b;
			currentTallest = getOriginalHeight(c);
			rowDivs.push(c)
		} else {
			rowDivs.push(c);
			currentTallest = (currentTallest < getOriginalHeight(c)) ? (getOriginalHeight(c)) : (currentTallest)
		}
		for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
			setConformingHeight(rowDivs[currentDiv], currentTallest)
		}
	})
};