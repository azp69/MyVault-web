    
    class AES
    {
        static encrypt (key, iv, message) {
            var encrypted = CryptoJS.AES.encrypt(message, key, { iv: CryptoJS.enc.Hex.parse(iv) })
            var base64 = encrypted.ciphertext.toString(CryptoJS.enc.Base64)
            // FOR DEBUG ONLY
            console.log("ENCRYPTED MESSAGE:" + base64);
            return base64;
        }

        static decrypt(key, iv, message) {
            var cipherParams = CryptoJS.lib.CipherParams.create({
                ciphertext: CryptoJS.enc.Base64.parse(message)
            });
            var decrypted = CryptoJS.AES.decrypt(cipherParams, key, { iv: CryptoJS.enc.Hex.parse(iv), format: CryptoJS.format.Hex });
            // FOR DEBUG ONLY
            console.log("DECRYPTED MESSAGE:" + decrypted.toString(CryptoJS.enc.Utf8));
            return decrypted.toString(CryptoJS.enc.Utf8)
        }

        static generateKey (salt, passPhrase) {
            var key = CryptoJS.PBKDF2(passPhrase, CryptoJS.enc.Hex.parse(suola), { keySize: 256/32, hasher: CryptoJS.algo.SHA256, iterations: 1000 });
            // FOR DEBUG ONLY
            console.log("GENERATED KEY:" + key);
            return key;
        }

        static generateIv()
        {
            var iv = CryptoJS.lib.WordArray.random(128/8).toString(CryptoJS.enc.Hex);
            // FOR DEBUG ONLY
            console.log("GENERATED IV:" + iv);
            return iv;
        }

        static generateSalt()
        {
            var salt = CryptoJS.lib.WordArray.random(128/8).toString(CryptoJS.enc.Hex);
            // FOR DEBUG ONLY
            console.log("GENERATED SALT:" + salt);
            return salt;
        }
    }