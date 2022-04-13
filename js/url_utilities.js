export function makeDefaultFilename(url) {
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