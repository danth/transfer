import { expect } from "chai";

import { makeDefaultFilename } from "../../js/url_utilities.js";

describe("makeDefaultFilename", function () {
  it("should extract a filename with no trailing slash", function() {
    const filename = makeDefaultFilename("https://example.com/file.txt");
    expect(filename).to.equal("file.txt");
  });

  it("should extract a filename with a trailing slash", function() {
    const filename = makeDefaultFilename("https://example.com/file.txt/");
    expect(filename).to.equal("file.txt");
  });

  it("should return `null` when there is no filename and no trailing slash", function() {
    const filename = makeDefaultFilename("https://example.com");
    expect(filename).to.be.null;
  });

  it("should return `null` when there is no filename and a trailing slash", function() {
    const filename = makeDefaultFilename("https://example.com/");
    expect(filename).to.be.null;
  });

  it("should return `null` when the URL is blank", function() {
    const filename = makeDefaultFilename("");
    expect(filename).to.be.null;
  });

  it("should return `null` when the URL is invalid", function() {
    const filename = makeDefaultFilename("abcdef");
    expect(filename).to.be.null;
  });
});
