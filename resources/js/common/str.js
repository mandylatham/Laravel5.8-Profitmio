export default class Str {
    static snake(data, delimiter = '_') {
        var data = data.toLowerCase();
        var breaks = data.match(/[^a-zA-Z]*/g);
        var result = [];

        for (var i=0; i < data.length; i++) {
            if (breaks.indexOf(data[i]) !== -1) {
                result.push('_');
            } else {
                result.push(data[i]);
            }
        }

        return result.join('');
    }
}
