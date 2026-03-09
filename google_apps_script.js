/**
 * Monitoring Money - MASTER Google Apps Script Backend (Hanya untuk Developer)
 * 
 * CARA PENGGUNAAN (UNTUK DEVELOPER):
 * 1. Buka https://script.google.com/ dan buat Project baru.
 * 2. Hapus semua kode default, dan salin/tempel KODE INI ke sana.
 * 3. Simpan (Ctrl+S).
 * 4. Klik tombol "Deploy" (Pojok kanan atas) -> "New deployment"
 * 5. Pilih tipe "Web app"
 * 6. Setting konfigurasi:
 *    - Execute as: "Me" (Email Anda)
 *    - Who has access: "Anyone"
 * 7. Klik "Deploy" dan izinkan otorisasi (Authorize access) jika diminta.
 * 8. Salin URL "Web app" yang diberikan.
 * 9. Buka file `script.js` di projek komputer Anda, lalu paste URL tersebut ke variabel `MASTER_SCRIPT_URL`.
 */

function doPost(e) {
  try {
    var rawData = e.postData.contents;
    var payload = JSON.parse(rawData);
    
    var sheetUrl = payload.sheetUrl;
    var data = payload.transaction;

    if (!sheetUrl) {
      throw new Error("URL Spreadsheet tidak ditemukan dalam request.");
    }
    
    // Buka Sheet berdasarkan URL yang diberikan user dari Frontend
    var doc = SpreadsheetApp.openByUrl(sheetUrl);
    var sheet = doc.getSheets()[0]; // Ambil sheet pertama
    
    // Jika sheet masih kosong, buatkan Header otomatis
    if (sheet.getLastRow() === 0) {
      sheet.appendRow(['ID', 'Tanggal', 'Tipe', 'Kategori', 'Nominal', 'Catatan', 'Timestamp', 'Profil']);
      // Styling header sedikit
      sheet.getRange(1, 1, 1, 8).setFontWeight("bold").setBackground("#f3f4f6");
    }

    // Gunakan profil dari payload atau default "Utama"
    var profile = data.profile || 'Utama';

    // Tulis baris baru transaksi di bawah
    sheet.appendRow([
      data.id,
      data.date,
      data.type,
      data.category,
      data.amount,
      data.note || '',
      data.timestamp || new Date(),
      profile // Tambahkan profil
    ]);
    
    // Format response sukses
    return ContentService
      .createTextOutput(JSON.stringify({ 'result': 'success', 'row': sheet.getLastRow() }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    // Format response gagal
    return ContentService
      .createTextOutput(JSON.stringify({ 'result': 'error', 'error': error.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

// Handler GET request untuk load data ke Frontend (Menggunakan JSONP)
function doGet(e) {
  try {
    var sheetUrl = e.parameter.sheetUrl;
    var callback = e.parameter.callback; // Menerima parameter callback untuk JSONP
    
    if (!sheetUrl) {
      return ContentService.createTextOutput("Monitoring Money Master API is running! Provide ?sheetUrl parameter to fetch data.");
    }

    var doc = SpreadsheetApp.openByUrl(sheetUrl);
    var sheet = doc.getSheets()[0];
    
    var ownerEmail = doc.getOwner() ? doc.getOwner().getEmail() : '';
    var docName = doc.getName() || 'Spreadsheet';
    
    var lastRow = sheet.getLastRow();
    if (lastRow <= 1) {
       // Sheet kosong atau cuma header
       var emptyResponse = JSON.stringify({ 'result': 'success', 'data': [], 'ownerEmail': ownerEmail, 'docName': docName });
       if (callback) {
          return ContentService.createTextOutput(callback + '(' + emptyResponse + ')')
            .setMimeType(ContentService.MimeType.JAVASCRIPT);
       }
       return ContentService.createTextOutput(emptyResponse).setMimeType(ContentService.MimeType.JSON);
    }
    
    // Asumsi header di row 1 (A1:H1)
    // ID | Tanggal | Tipe | Kategori | Nominal | Catatan | Timestamp | Profil
    var dataRange = sheet.getRange(2, 1, lastRow - 1, 8);
    var rawValues = dataRange.getValues();
    
    var formattedData = [];
    for (var i = 0; i < rawValues.length; i++) {
       var row = rawValues[i];
       formattedData.push({
         id: row[0],
         date: row[1],
         type: row[2],
         category: row[3],
         amount: row[4],
         note: row[5],
         timestamp: row[6],
         profile: row[7] || 'Utama' // Ambil profil dari kolom H
       });
    }

    var successResponse = JSON.stringify({ 'result': 'success', 'data': formattedData, 'ownerEmail': ownerEmail, 'docName': docName });
    
    // Kembalikan sebagai JSONP jika ada callback, kalau tidak JSON biasa
    if (callback) {
      return ContentService.createTextOutput(callback + '(' + successResponse + ')')
        .setMimeType(ContentService.MimeType.JAVASCRIPT);
    }
    
    return ContentService.createTextOutput(successResponse)
      .setMimeType(ContentService.MimeType.JSON);

  } catch (error) {
     var errorResponse = JSON.stringify({ 'result': 'error', 'error': error.toString() });
     if (e.parameter.callback) {
         return ContentService.createTextOutput(e.parameter.callback + '(' + errorResponse + ')')
           .setMimeType(ContentService.MimeType.JAVASCRIPT);
     }
     return ContentService.createTextOutput(errorResponse).setMimeType(ContentService.MimeType.JSON);
  }
}
