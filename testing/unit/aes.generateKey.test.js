
describe('AES.generateKey(salt, passPhrase).toString()', function () {
    it('Should return "d49e9975fbda999aa24d74d761c60f16d8d7048485688894fd4328420098fcdd" for salt "salt" and passphrase "passPhrase"', function () {
      chai.expect(AES.generateKey("salt", "passPhrase").toString()).to.equal("d49e9975fbda999aa24d74d761c60f16d8d7048485688894fd4328420098fcdd");
    });
});
