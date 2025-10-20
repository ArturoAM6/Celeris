// Certificado público (contenido de qz-cert.pem)
var CERTIFICATE =
  "-----BEGIN CERTIFICATE-----\n" +
  "MIIDDjCCAfagAwIBAgIQQPNT4rVHpJpKdV0tkeIh/DANBgkqhkiG9w0BAQsFADAUMRIwEAYDVQQDDAlsb2NhbGhvc3QwHhcNMjUxMDIwMDM1NDE3WhcNMzUxMDIwMDQwNDE2WjAUMRIwEAYDVQQDDAlsb2NhbGhvc3QwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQCxD3iDXYfWDUGZWSDD/iUHb0KzMfqI4DQ19Jo5mgho4Rapa4LJJxMfLVQ4WcjqLiNbcALADrzlqA/j1/ZN7I35OCfX3pTrnFXIBVBQFw+KM2JMaDrd57JIOEXkglrkV0Poo9cH2Vg5lAtrW6u5l3Gm6YTqZt9/ey17p9IL9ckQglqNKZjt8n0Cj4sb90AO5L1+Yuwd+dYe+balaMLz/3BtI9uUa5FZvpnpNalEyrULQsxu9ozW+ucC3JlhN85eTKTxBSA+OjV6evE1oxs/hNavEqKb8PP23qZPjQkXP8HHW4ryOVYpQGsPHxDTc8z0NEJoq7EkPmB3WIguwoabVnMxAgMBAAGjXDBaMA4GA1UdDwEB/wQEAwIHgDATBgNVHSUEDDAKBggrBgEFBQcDAzAUBgNVHREEDTALgglsb2NhbGhvc3QwHQYDVR0OBBYEFCXyeYNIqTbXBE+D8h/Przw90ZB5MA0GCSqGSIb3DQEBCwUAA4IBAQB1OdW3HwFtCWD2wJ5bJ0VUAWawQnWtElJBkI7skOaopvWlt2pQNGxa0SbIkal/agWgbASBXwib0+G8ct1bPpGnF6VkfiMUSdNrmN3t/+xCTPCvewdjv30WELOVrs/RdJEACXAPj2H1AhKX2mn6Hj+rdlXLDsmIH58vvQIeXhNqLydgTk3TQsDnjn7xu2XVBTE5JdUb3BpXqOOQKIN6CK4eXE5fMldycC0bBLGUGiAAt9qSk92o10bXNVfRvMHg+eLBNqbAt62VDK2eVFcNq0GLIgcrngS6hVXND3WCwgoaZCvLgb29xOFlkz7achBWEUAE8VZZ54DJoS53gEs465Hg\n" + // PEGA AQUÍ EL CONTENIDO DE qz-cert.pem
  "-----END CERTIFICATE-----\n";

// Clave privada (contenido de qz-private-key.pem)
var PRIVATE_KEY =
  "-----BEGIN PRIVATE KEY-----\n" +
  "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCxD3iDXYfWDUGZWSDD/iUHb0KzMfqI4DQ19Jo5mgho4Rapa4LJJxMfLVQ4WcjqLiNbcALADrzlqA/j1/ZN7I35OCfX3pTrnFXIBVBQFw+KM2JMaDrd57JIOEXkglrkV0Poo9cH2Vg5lAtrW6u5l3Gm6YTqZt9/ey17p9IL9ckQglqNKZjt8n0Cj4sb90AO5L1+Yuwd+dYe+balaMLz/3BtI9uUa5FZvpnpNalEyrULQsxu9ozW+ucC3JlhN85eTKTxBSA+OjV6evE1oxs/hNavEqKb8PP23qZPjQkXP8HHW4ryOVYpQGsPHxDTc8z0NEJoq7EkPmB3WIguwoabVnMxAgMBAAECggEBAKQ8/IWmIZUtMLdFFH6Y5NIzo0RDTOjgR63w2Yoq1jgq6nF215ctFrCpxGCM8DBBYey0RujOAuxoa2zma4M2tS8Cpvq3bZ3royppu2i/K1v/c+P6HvitrhMNl89yF0uNouFN8O1H721hZQJNw0nhL7wrCm6/w/slUDhOoCkV8U7MgBv/U3HVkyUSIkIW5+TL58S93ht3+naOF/CnvliWI8bmZNm4AFqZy3T769x4ApK5g46TcajTOIyhl9EkP0qMaAZclITo7z2z5zoiaDymYzssoQvZwGtRiGuxR4xQ4loppl6UWfagLOBKc2z9xXkxRTvEETiBqCNUetIBR5rRbm0CgYEAygte6xJ22EhrE1afK2XFELO0dqEZbYjd6fsrlwwDVx8jXEpwtpmSxI13sDDqiJVe+eP7eD8FJ5r2zk8Xf7QMmoneXjWUatGuH7CLVGQ/5Ccfb8bjOrjw6bfCdT2YHJlqMRpJFvwNrCao6wPo9zCk2geBRWGUAx6gi1f80G8RcgsCgYEA4FgWPOjQvhJBYEJTel2I0Qr0Hb9Am7uUzgWkrnrb9B/RSpGyR3v2bWlJqnZWTOq9lG2VLqjTe/PIcrSFPykTDO477p6O9UlBxD3ycYEz7SnnKYPhXc4PRn+rzj94YGCkC2094q3ChDgxrPP8GsSY4p7jI4ymS0BVYxz0dP8eETMCgYBwP6T6QIXaX1FtqwA/Igk74DrwdUwlOJbheXOcNUZdzqTcj1bTe7q4jEfkkSibvTDI8EozYf/BIyzfLb3GawddjB8IhAfy2I8+d9zQg+mjHcEvXnW7mCfwEPFuJUwvB2Sh5xKYPGx0Kf5Kox94xYOwxd6h5zZODWPwRm/kdPBiRQKBgQCj2ZH8UGnMuFaJMEf50gCP9LkQVNOiKiFnSxXY3SByLx7ToI+dx/rWNBS6bA1hxfxKQLK5TlKPcCBRLmk3FQ+13PVmtOViXmurITdGEQBU0crkNk7ODSZ47dwUGaUYdty4/8M4III1wTj+wX+6KSYkNbldCGeYBFeYuNgN7xoNowKBgBIX00MNHriI78LisWX54abW17pexSlf5TXUQF9HvNvtvi7elm93lFrS0XrB2KQOjMWBIRtTXtwQHqY0jYSJrxG49L0dxM+WYQ/7iMSRuGOomDgs0xB8kxM84BXL8kTMxWYATB2z8IiQNZ4zdPlZ3Rx53iJIJ18PuBM6rTqIXlMQ\n" + // PEGA AQUÍ EL CONTENIDO DE qz-private-key.pem
  "-----END PRIVATE KEY-----\n";

qz.security.setCertificatePromise(function (resolve, reject) {
  resolve(CERTIFICATE);
});

qz.security.setSignatureAlgorithm("SHA512");

qz.security.setSignaturePromise(function (toSign) {
  return function (resolve, reject) {
    try {
      var pk = KEYUTIL.getKey(PRIVATE_KEY);
      var sig = new KJUR.crypto.Signature({ alg: "SHA512withRSA" });
      sig.init(pk);
      sig.updateString(toSign);
      var hex = sig.sign();
      resolve(stob64(hextorstr(hex)));
    } catch (err) {
      console.error(err);
      reject(err);
    }
  };
});

function stob64(str) {
  return btoa(str);
}

function hextorstr(hex) {
  var str = "";
  for (var i = 0; i < hex.length; i += 2) {
    str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
  }
  return str;
}
