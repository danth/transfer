function getFilename(url) {
		let segments;
		try {
			segments = new URL(url).pathname.split("/");
		} catch (TypeError) {
			// Do nothing if the URL fails to parse.
			return null;
		}

		// The || handles the possibility of a trailing slash.
		return segments.pop() || segments.pop() || null;
}

const EXTENSION = /\.((?:tar\.)?[^/.]+)$/;

export function makeDefaultFilename(url) {
		let filename = getFilename(url);
		if (!filename) return null;
		return filename.replace(EXTENSION, "");
}

export function makeDefaultExtension(url) {
		let filename = getFilename(url);
		if (!filename) return null;
		const match = filename.match(EXTENSION);
		return match ? match[1] : null;
}