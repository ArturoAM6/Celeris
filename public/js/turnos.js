document.getElementById("btnImprimirPDF").addEventListener("click", function() {
    let pdfUrl = "generar_pdf.php";
    
    const iframe = document.createElement("iframe");
    iframe.style.display = "none";
    iframe.src = pdfUrl; 

    iframe.onload = function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    };

    document.body.appendChild(iframe);
});