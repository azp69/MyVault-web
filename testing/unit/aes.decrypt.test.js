describe('AES.decrypt(key, iv, message)', function () {
    it('Should return "test" as decrypted message "TIy9lQulO/N7TjyHDE2Bjw==" with iv: "f9e0aa853b4b54fb6ab403508d975bb8" and key "d49e9975fbda999aa24d74d761c60f16d8d7048485688894fd4328420098fcdd"', function () {
        var key = CryptoJS.enc.Hex.parse("d49e9975fbda999aa24d74d761c60f16d8d7048485688894fd4328420098fcdd");
        var iv = "f9e0aa853b4b54fb6ab403508d975bb8";
        var message = "TIy9lQulO/N7TjyHDE2Bjw==";
        chai.expect(AES.decrypt(key, iv, message).toString()).to.equal("test");
    });
    it('Should return "" while decrypting message "TIy9lQulO/N7TjyHDE2Bjw==" with iv: "f9e0aa853b4b54fb6ab403508d975bb8" and wrong key "a49e9975fbda999aa24d74d761c60f16d8d7048485688894fd4328420098fcdd"', function () {
        var key = CryptoJS.enc.Hex.parse("a49e9975fbda999aa24d74d761c60f16d8d7048485688894fd4328420098fcdd");
        var iv = "f9e0aa853b4b54fb6ab403508d975bb8";
        var message = "TIy9lQulO/N7TjyHDE2Bjw==";
        chai.expect(AES.decrypt(key, iv, message).toString()).to.equal("");
    });
});



