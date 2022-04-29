import { expect } from "chai";

import { makeDefaultFilename, makeDefaultExtension } from "../../js/url_utilities.js";

describe("makeDefaultFilename", function () {
  it("should extract a filename with no trailing slash", function() {
    const filename = makeDefaultFilename("https://example.com/file.txt");
    expect(filename).to.equal("file");
  });

  it("should extract a filename with a trailing slash", function() {
    const filename = makeDefaultFilename("https://example.com/file.txt/");
    expect(filename).to.equal("file");
  });

  it("should treat .tar.gz as an extension", function() {
    const filename = makeDefaultFilename("https://example.com/archive.tar.gz");
    expect(filename).to.equal("archive");
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

describe("makeDefaultExtension", function () {
  it("should extract an extension with no trailing slash", function() {
    const extension = makeDefaultExtension("https://example.com/file.txt");
    expect(extension).to.equal("txt");
  });

  it("should extract an extension with a trailing slash", function() {
    const extension = makeDefaultExtension("https://example.com/file.txt/");
    expect(extension).to.equal("txt");
  });

  it("should treat .tar.gz as an extension", function() {
    const extension = makeDefaultExtension("https://example.com/archive.tar.gz");
    expect(extension).to.equal("tar.gz");
  });

  it("should return `null` when there is no extension and no trailing slash", function() {
    const extension = makeDefaultExtension("https://example.com");
    expect(extension).to.be.null;
  });

  it("should return `null` when there is no extension and a trailing slash", function() {
    const extension = makeDefaultExtension("https://example.com/");
    expect(extension).to.be.null;
  });

  it("should return `null` when there is no extension and no trailing slash", function() {
    const extension = makeDefaultExtension("https://example.com/file");
    expect(extension).to.be.null;
  });

  it("should return `null` when there is no extension and a trailing slash", function() {
    const extension = makeDefaultExtension("https://example.com/file/");
    expect(extension).to.be.null;
  });

  it("should return `null` when the URL is blank", function() {
    const extension = makeDefaultExtension("");
    expect(extension).to.be.null;
  });

  it("should return `null` when the URL is invalid", function() {
    const extension = makeDefaultExtension("abcdef");
    expect(extension).to.be.null;
  });
});
