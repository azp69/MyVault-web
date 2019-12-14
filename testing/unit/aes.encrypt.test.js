describe('AES.encrypt(key, iv, message)', function () {
    it('Should return "TIy9lQulO/N7TjyHDE2Bjw==" as encrypted message for "test" with iv: "f9e0aa853b4b54fb6ab403508d975bb8" and key "d49e9975fbda999aa24d74d761c60f16d8d7048485688894fd4328420098fcdd"', function () {
        var key = CryptoJS.enc.Hex.parse("d49e9975fbda999aa24d74d761c60f16d8d7048485688894fd4328420098fcdd");
        var iv = "f9e0aa853b4b54fb6ab403508d975bb8";
        var message = "test";
        chai.expect(AES.encrypt(key, iv, message).toString()).to.equal("TIy9lQulO/N7TjyHDE2Bjw==");
    });
});