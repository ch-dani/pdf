window.tit = 0;
var curve3_count = 0;

var rect_transform = false;
var rectToPath = function(x, y, ops, args, trans, ctx){
	if(typeof _util=='undefined'){ var _util = pdfjsLib; }
	var path_arr = [];
	
	rect_transform = ctx.mozCurrentTransform;

	for (var i = 0, j = 0, ii = ops.length; i < ii; i++) {
		var op = ops[i];
		try {
			//TODO
			switch (op | 0) {
				case _util.OPS.rectangle:
					x = args[j++];
					y = args[j++];
					var width = args[j++];
					var height = args[j++];
					var xw = x + width;
					var yh = y + height;
					
					
					pair1 = proccessRectCords(pf(x), pf(y));
					pair2 = proccessRectCords(pf(xw), pf(y));
					pair3 = proccessRectCords(pf(xw), pf(yh));
					pari4 = proccessRectCords(pf(x), pf(yh));
					
					path_arr.push('M', ...pair1, 'L', ...pair2 , 'L', ...pair3, 'L', ...pari4, 'Z');
					break;

				case _util.OPS.moveTo:
					x = args[j++];
					y = args[j++];
					var pairs = proccessRectCords(pf(x), pf(y));
					path_arr.push('M', ...pairs);
					break;
				case _util.OPS.lineTo:
					x = args[j++];
					y = args[j++];
					var pairs = proccessRectCords(pf(x), pf(y));
					path_arr.push('L', ...pairs);
					break;

				case _util.OPS.curveTo:
					x = args[j + 4];
					y = args[j + 5];
					var pairs = proccessRectCords(pf(args[j]), pf(args[j + 1]), pf(args[j + 2]), pf(args[j + 3]), pf(x), pf(y));
					path_arr.push('C', ...pairs);

					//path_arr.push('C', pf(args[j]), trans.f-pf(args[j + 1]), pf(args[j + 2]), trans.f - pf(args[j + 3]), pf(x), trans.f - pf(y));
					j += 6;
					break;

				case _util.OPS.curveTo2:
				
					x = args[j + 2];
					y = args[j + 3];

					var pairs = proccessRectCords(pf(x), pf(y), pf(args[j]), pf(args[j + 1]), pf(args[j + 2]), pf(args[j + 3]));
					path_arr.push('C', ...pairs);
					
					//path_arr.push('C', pf(x), pf(y), pf(args[j]), pf(args[j + 1]), pf(args[j + 2]), pf(args[j + 3]));
					j += 4;
					break;

				case _util.OPS.curveTo3:
					curve3_count++;
					
					
					x = args[j + 2];
					y = args[j + 3];

					var pairs = proccessRectCords(pf(args[j]), pf(args[j + 1]), pf(x), pf(y), pf(x), pf(y));
					path_arr.push('C', ...pairs);

					//path_arr.push('C', pf(args[j]), trans.f-pf(args[j + 1]), pf(x), trans.f-pf(y), pf(x), trans.f-pf(y));
					j += 4;
					break;

				case _util.OPS.closePath:
					path_arr.push('Z');
					break;
			}
		} catch (e) {
			console.error("can't create rect path", e);
			return false;
		}
	}
	window.tit++;
	return path_arr;
}

function proccessRectCords(...args){
	var pairs = splitPairs(args);
	var nw = [];
	$.each(pairs, function(i,v){
		var real_pair = pdfjsLib.Util.applyTransform([v[0],v[1]], rect_transform);
		real_pair = real_pair.map((vv)=>{ return pf(vv); })
		nw = nw.concat(real_pair);
	});
	return nw;
}


function proccessTrans(transform, ...args){
	var pairs = splitPairs(args);
	var nw = [];
	$.each(pairs, function(i,v){
		var real_pair = pdfjsLib.Util.applyTransform([v[0],v[1]], transform);
		real_pair = real_pair.map((vv)=>{ return pf(vv); })
		nw = nw.concat(real_pair);
	});
	return nw;
}



function splitPairs(arr){
	var pairs = [];
	for(var i=0 ; i<arr.length ; i+=2){
		if(arr[i+1] !== undefined){
			pairs.push([arr[i], arr[i+1]]);
		}else{
			pairs.push([arr[i]]);
		}
	}
	return pairs;
};

var pf = function pf(value) {
	if (Number.isInteger(value)) {
		return value.toString();
	}
	
	var s = value.toFixed(10);
	var i = s.length - 1;
	if (s[i] !== '0') {
		return s;
	}

	do {
		i--;
	} while (s[i] === '0');

	return s.substring(0, s[i] === '.' ? i : i + 1);
};

var pm = function pm(m) {
	if (m[4] === 0 && m[5] === 0) {
		if (m[1] === 0 && m[2] === 0) {
			if (m[0] === 1 && m[3] === 1) {
				return '';
			}
			return "scale(".concat(pf(m[0]), " ").concat(pf(m[3]), ")");
		}
		if (m[0] === m[3] && m[1] === -m[2]) {
			var a = Math.acos(m[0]) * 180 / Math.PI;
			return "rotate(".concat(pf(a), ")");
		}
	} else {
		if (m[0] === 1 && m[1] === 0 && m[2] === 0 && m[3] === 1) {
			return "translate(".concat(pf(m[4]), " ").concat(pf(m[5]), ")");
		}
	}

	return "matrix(".concat(pf(m[0]), " ").concat(pf(m[1]), " ").concat(pf(m[2]), " ").concat(pf(m[3]), " ").concat(pf(m[4]), " ") + "".concat(pf(m[5]), ")");
};


function trans1(p, m){
	var xt = p[0] * m[0] + p[1] * m[2] + m[4];
	var yt = p[0] * m[1] + p[1] * m[3] + m[5];
	return [xt, yt];
};

async function getCanvasBlob(canvas, th) {
	return new Promise(function(resolve, reject) {
		canvas.toBlob(function(blob) {
			var newImg = document.createElement('img'),
			url = URL.createObjectURL(blob);
			newImg.src = url;
			resolve([url, newImg, canvas, th]);
		});
	});
}






