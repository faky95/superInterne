function explodeFronStringToString(arg, delimiter) {
	str = '';
	if(typeof arg !== 'undefined' && arg !== false && arg !== null) {
		data = arg.split(' ');
		for (var key in data) {
			str = ", '" + data[key] + "'";
		}
	}
	return str;
}