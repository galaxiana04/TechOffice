<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Weibull - Langkah demi Langkah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1,
        h2 {
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .step {
            margin-bottom: 40px;
        }

        .formula {
            font-style: italic;
            margin: 10px 0;
        }

        .ref {
            font-size: 0.9em;
            color: #555;
        }
    </style>
</head>

<body>

    <a href="{{ route('weibull.dashboard') }}" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>
    <h1>Estimasi β, η, dan MTBF Menggunakan Distribusi Weibull</h1>


    {{-- Asumsi --}}
    <div class="card border-start-primary shadow-sm mb-4">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-info-circle-fill me-2"></i>
                Asumsi Analisis Weibull
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="text-center">
                        <span class="badge bg-danger fs-6 py-2 px-4 mb-3">Repairable (Repairable As New)</span>
                        <p class="fw-bold text-danger">Dapat diperbaiki</p>
                        <ul class="text-start small text-muted">
                            <li>Komponen diperbaiki kembali ke kondisi semula setelah kegagalan.</li>
                            <li>Semua kegagalan dihitung untuk <strong>MTBF</strong> menggunakan distribusi Weibull.
                            </li>
                            <li>Asumsi perbaikan → unit kembali seperti baru, sehingga perhitungan MTBF sejalan dengan
                                metode MTTF.</li>
                            <li>TBF start 0 dihitung per unit setelah perbaikan jika masih repair</li>
                            <li>TBF start 0 dihitung unit baru dipasang jika memang benar2 tidak bisa diperbaiki lagi
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-center">
                        <span class="badge bg-info fs-6 py-2 px-4 mb-3">Non-Repairable</span>
                        <p class="fw-bold text-info">Tidak dapat diperbaiki</p>
                        <ul class="text-start small text-muted">
                            <li>Setelah kegagalan, komponen diganti baru.</li>
                            <li>Lifetime dihitung dari <strong>TTF unit baru</strong> saja (MTTF) menggunakan distribusi
                                Weibull.</li>
                            <li>Metode perhitungan sama dengan MTBF, hanya berbeda treatment kegagalan (tidak ada
                                perbaikan).</li>
                            <li>TTF start 0 dihitung hanya untuk unit baru yang dipasang.</li>
                            <li>TTF pasti start 0 saat dipasang karena barang non-repairable.</li>
                        </ul>
                    </div>
                </div>
            </div>


        </div>
    </div>



    {{-- LANGKAH DEMI LANGKAH --}}



    <div class="step">
        <h2>Langkah 1: Hitung Time To Failure (TTF)</h2>
        <p>TTF dihitung dalam jam operasi efektif, dengan mempertimbangkan waktu operasi harian (12 jam/hari), hari
            kerja per minggu (6 hari), dan libur. TTF = jumlah hari kerja efektif × 12 jam.</p>
        <table>
            <tr>
                <th>Kejadian</th>
                <th>Tgl Gagal</th>
                <th>Jam Gagal</th>
                <th>Tgl Start</th>
                <th>Hari Kerja</th>
                <th>TTF (jam)</th>
            </tr>
            <tr>
                <td>1</td>
                <td>05/06/2023</td>
                <td>7:00</td>
                <td>01/06/2023</td>
                <td>4</td>
                <td>55</td>
            </tr>
            <tr>
                <td>2</td>
                <td>05/06/2023</td>
                <td>10:00</td>
                <td>01/06/2023</td>
                <td>4</td>
                <td>58</td>
            </tr>
            <tr>
                <td>3</td>
                <td>15/10/2023</td>
                <td>7:00</td>
                <td>01/06/2023</td>
                <td>117</td>
                <td>1411</td>
            </tr>
            <tr>
                <td>4</td>
                <td>09/11/2023</td>
                <td>13:00</td>
                <td>01/06/2023</td>
                <td>139</td>
                <td>1681</td>
            </tr>
            <tr>
                <td>5</td>
                <td>25/11/2023</td>
                <td>8:00</td>
                <td>01/06/2023</td>
                <td>153</td>
                <td>1844</td>
            </tr>
        </table>
        <p class="ref">Referensi: Data dummy.</p>
    </div>

    <div class="step">
        <h2>Langkah 2: Urutkan TTF Ascending</h2>
        <table>
            <tr>
                <th>i</th>
                <th>TTF (jam)</th>
            </tr>
            <tr>
                <td>1</td>
                <td>55</td>
            </tr>
            <tr>
                <td>2</td>
                <td>58</td>
            </tr>
            <tr>
                <td>3</td>
                <td>1411</td>
            </tr>
            <tr>
                <td>4</td>
                <td>1681</td>
            </tr>
            <tr>
                <td>5</td>
                <td>1844</td>
            </tr>
        </table>
    </div>

    <div class="step">
        <h2>Langkah 3: Hitung Probabilitas kegagalan dengan Median Rank (Benard Approximation)
        </h2>
        <p class="formula">Median Rank F(i) ≈ (i - 0.3) / (n + 0.4), di mana n = 5</p>
        <table>
            <tr>
                <th>i</th>
                <th>TTF (jam)</th>
                <th>Median Rank (Benard)</th>
            </tr>
            <tr>
                <td>1</td>
                <td>55</td>
                <td>0.13</td>
            </tr>
            <tr>
                <td>2</td>
                <td>58</td>
                <td>0.31</td>
            </tr>
            <tr>
                <td>3</td>
                <td>1411</td>
                <td>0.50</td>
            </tr>
            <tr>
                <td>4</td>
                <td>1681</td>
                <td>0.69</td>
            </tr>
            <tr>
                <td>5</td>
                <td>1844</td>
                <td>0.87</td>
            </tr>
        </table>
        <p class="ref">Referensi Rumus Isograph:
            file:///C:/ProgramData/Isograph/Reliability%20Workbench/15.0/eHelp/index.htm#t=Weibull_Set_Form_Analysis.htm&rhsearch=Weibull%20Analysis%20Options&rhhlterm=weibull%20analysis%20options
            .
            Sumber: ReliaWiki (ReliaSoft), reliability.readthedocs.io, dan Weibull.com HotWire.</p>
    </div>

    <div class="step">
        <h2>Langkah 4: Transformasi untuk Weibull Probability Plot</h2>

        <p>
            Distribusi Weibull dua parameter didefinisikan oleh fungsi distribusi kumulatif (CDF) sebagai:
        </p>

        <p class="formula">
            F(t) = 1 - exp [ - (t / η)<sup>β</sup> ]
        </p>

        <p>
            Untuk memperoleh hubungan linier, persamaan CDF tersebut ditransformasikan melalui
            pengambilan logaritma natural dua kali sebagai berikut:
        </p>

        <ol>
            <li>
                Memindahkan suku eksponensial:
                <br>
                <span class="formula">1 - F(t) = exp [ - (t / η)<sup>β</sup> ]</span>
            </li>

            <li>
                Mengambil logaritma natural:
                <br>
                <span class="formula">ln(1 - F(t)) = - (t / η)<sup>β</sup></span>
            </li>

            <li>
                Mengalikan dengan −1:
                <br>
                <span class="formula">-ln(1 - F(t)) = (t / η)<sup>β</sup></span>
            </li>

            <li>
                Mengambil logaritma natural kembali:
                <br>
                <span class="formula">ln[-ln(1 - F(t))] = β ln(t) − β ln(η)</span>
            </li>
        </ol>

        <p>
            Persamaan di atas membentuk hubungan linier:
        </p>

        <p class="formula">
            Y = bX + c
        </p>

        <p>
            dengan transformasi:
        </p>

        <p class="formula">
            X = ln(TTF)<br>
            Y = ln[-ln(1 - F)]
        </p>

        <p>
            Parameter regresi memiliki makna:
        </p>

        <ul>
            <li><strong>b = β</strong> → parameter bentuk (shape parameter)</li>
            <li><strong>c = −β ln(η)</strong> → konstanta terkait umur karakteristik</li>
        </ul>

        <table>
            <tr>
                <th>i</th>
                <th>TTF</th>
                <th>X = ln(TTF)</th>
                <th>F (Median Rank)</th>
                <th>Y = ln(-ln(1-F))</th>
            </tr>
            <tr>
                <td>1</td>
                <td>55</td>
                <td>4.007</td>
                <td>0.13</td>
                <td>-1.974</td>
            </tr>
            <tr>
                <td>2</td>
                <td>58</td>
                <td>4.060</td>
                <td>0.31</td>
                <td>-0.973</td>
            </tr>
            <tr>
                <td>3</td>
                <td>1411</td>
                <td>7.252</td>
                <td>0.50</td>
                <td>-0.367</td>
            </tr>
            <tr>
                <td>4</td>
                <td>1681</td>
                <td>7.427</td>
                <td>0.69</td>
                <td>0.145</td>
            </tr>
            <tr>
                <td>5</td>
                <td>1844</td>
                <td>7.520</td>
                <td>0.87</td>
                <td>0.714</td>
            </tr>
        </table>

        <p class="ref">
            Referensi Rumus Distribusi Weibull :
            file:///C:/ProgramData/Isograph/Reliability%20Workbench/15.0/eHelp/index.htm#t=Weibull_Distributions.htm&rhsearch=Weibull%20Analysis%20Options&rhhlterm=weibull%20option
        </p>
    </div>

    <div class="step">
        <h2>Langkah 5: Hitung X dan Y (Transformasi Weibull)</h2>

        <p>
            Pada tahap ini dilakukan transformasi data agar persamaan distribusi Weibull dua parameter
            dapat dianalisis menggunakan regresi linear.
        </p>

        <p class="formula">
            X = ln(TTF)<br>
            Y = ln[-ln(1 − F)]
        </p>

        <table>
            <tr>
                <th>i</th>
                <th>X = ln(TTF)</th>
                <th>Y = ln(-ln(1−F))</th>
                <th>X·Y</th>
                <th>X²</th>
            </tr>
            <tr>
                <td>1</td>
                <td>4.007333185</td>
                <td>-1.974458694</td>
                <td>-7.912313849</td>
                <td>16.05871926</td>
            </tr>
            <tr>
                <td>2</td>
                <td>4.060443011</td>
                <td>-0.972686141</td>
                <td>-3.949536644</td>
                <td>16.48719744</td>
            </tr>
            <tr>
                <td>3</td>
                <td>7.252053952</td>
                <td>-0.366512921</td>
                <td>-2.657971474</td>
                <td>52.59228652</td>
            </tr>
            <tr>
                <td>4</td>
                <td>7.427144133</td>
                <td>0.144767396</td>
                <td>1.075208318</td>
                <td>55.16246998</td>
            </tr>
            <tr>
                <td>5</td>
                <td>7.519692404</td>
                <td>0.714455486</td>
                <td>5.372485493</td>
                <td>56.54577385</td>
            </tr>
            <tr>
                <th>Σ</th>
                <th>30.26666669</th>
                <th>-2.454434874</th>
                <th>-8.072128155</th>
                <th>196.8464471</th>
            </tr>
        </table>

        <p class="ref">Referensi mencari β / m / slop / kemiringan:
            https://www.statisticshowto.com/probability-and-statistics/statistics-definitions/what-is-a-regression-equation/
        </p>
    </div>
    <div class="step">
        <h2>Langkah 6: Regresi Linear untuk Menentukan β (Shape Parameter)</h2>

        <p>
            Parameter β diperoleh dari kemiringan (slope) hasil regresi linear antara Y dan X.
        </p>

        <p class="formula">
            β =
            [ n·Σ(XY) − (ΣX)(ΣY) ] /
            [ n·Σ(X²) − (ΣX)² ]
        </p>

        <table>
            <tr>
                <th>Parameter</th>
                <th>Nilai</th>
            </tr>
            <tr>
                <td>n</td>
                <td>5</td>
            </tr>
            <tr>
                <td>Σ(X·Y)</td>
                <td>-8.072128155</td>
            </tr>
            <tr>
                <td>ΣX</td>
                <td>30.26666669</td>
            </tr>
            <tr>
                <td>ΣY</td>
                <td>-2.454434874</td>
            </tr>
            <tr>
                <td>Σ(X²)</td>
                <td>196.8464471</td>
            </tr>
            <tr>
                <td>(ΣX)²</td>
                <td>916.0711122</td>
            </tr>
        </table>

        <p class="formula">
            β = (5 × -8.072128155 − 30.26666669 × -2.454434874)
            / (5 × 196.8464471 − 916.0711122)
        </p>

        <p>
            <strong>β = 0.4977459282</strong>
        </p>

        <p class="ref">Referensi mencari β / m / slop / kemiringan:
            https://www.statisticshowto.com/probability-and-statistics/statistics-definitions/what-is-a-regression-equation/
        </p>
    </div>

    <div class="step">
        <h2>Langkah 7: Hitung Konstanta Regresi (Intercept, c)</h2>

        <p>
            Pada tahap sebelumnya sempat terjadi kekeliruan karena nilai
            ΣX dan ΣY diambil dari <strong>satu titik data</strong>.
            Dalam regresi linear, konstanta <strong>c</strong> wajib dihitung
            menggunakan <u>seluruh data</u>.
        </p>

        <p class="formula">
            Persamaan regresi linear Weibull:<br>
            Y = βX + c
        </p>

        <p class="formula">
            Rumus konstanta regresi:<br>
            c = ( ΣY − β·ΣX ) / n
        </p>

        <table>
            <tr>
                <th>Parameter</th>
                <th>Nilai (Benar)</th>
            </tr>
            <tr>
                <td>β</td>
                <td>0.4977459282</td>
            </tr>
            <tr>
                <td>ΣX</td>
                <td>30.26666669</td>
            </tr>
            <tr>
                <td>ΣY</td>
                <td>-2.454434874</td>
            </tr>
            <tr>
                <td>n</td>
                <td>5</td>
            </tr>
        </table>

        <p class="formula">
            c = ( -2.454434874 − (0.4977459282 × 30.26666669) ) / 5
        </p>

        <p class="formula">
            c = ( -2.454434874 − 15.06609459 ) / 5
        </p>

        <p class="formula">
            c = -17.52052946 / 5
        </p>

        <p>
            <strong>c = -3.504105892</strong>
        </p>

        <p class="ref">
            Catatan: Nilai c = -0.7938 yang diperoleh dari satu titik data
            tidak digunakan karena tidak merepresentasikan regresi linear.
        </p>
    </div>


    <div class="step">
        <h2>Langkah 8: Hitung Parameter Skala η (Eta)</h2>

        <p>
            Parameter skala <strong>η</strong> menyatakan umur karakteristik,
            yaitu waktu ketika probabilitas kegagalan mencapai 63,2%.
            Nilai η dihitung dari konstanta regresi <strong>c</strong>.
        </p>

        <p class="formula">
            Hubungan konstanta regresi dengan parameter Weibull:<br>
            c = −β ln(η)
        </p>

        <p class="formula">
            Diturunkan menjadi:<br>
            ln(η) = −c / β
        </p>

        <p class="formula">
            η = exp( −c / β )
        </p>

        <table>
            <tr>
                <th>Parameter</th>
                <th>Nilai</th>
            </tr>
            <tr>
                <td>β</td>
                <td>0.4977459282</td>
            </tr>
            <tr>
                <td>c</td>
                <td>-3.504105892</td>
            </tr>
        </table>

        <p class="formula">
            η = exp( 3.504105892 / 0.4977459282 )
        </p>

        <p>
            <strong>η ≈ 1140 jam</strong>
        </p>

        <p class="ref">
            Referensi: Tinggal hitung dari persamaan yang sudah diturunkan
        </p>
    </div>



    <div class="step">
        <h2>Langkah 9: Hitung B10 & B25 (Weibull Life)</h2>

        <p>
            Nilai B<em>x</em> menyatakan waktu ketika tingkat keandalan
            mencapai nilai tertentu.
            Pada analisis Weibull, perhitungan dilakukan menggunakan
            <strong>reliability (R)</strong>, bukan langsung dari persentase kegagalan.
        </p>

        <p class="formula">
            Rumus Weibull life berbasis reliability:<br>
            t<sub>R</sub> = η × [ −ln(R) ]<sup>1/β</sup>
        </p>

        <table>
            <tr>
                <th>Dicari</th>
                <th>Reliability (R)</th>
                <th>Lifetime (jam)</th>
            </tr>
            <tr>
                <td>B10</td>
                <td>0.90</td>
                <td>12.40918359</td>
            </tr>
            <tr>
                <td>B25</td>
                <td>0.75</td>
                <td>93.36069227</td>
            </tr>
        </table>

        <p class="formula">
            B10 = η × [ −ln(0.90) ]<sup>1/β</sup>
        </p>

        <p class="formula">
            B25 = η × [ −ln(0.75) ]<sup>1/β</sup>
        </p>

        <p>
            Dengan parameter Weibull yang telah diperoleh sebelumnya,
            maka diperoleh:
            <br>
            <strong>B10 ≈ 12.41 jam</strong><br>
            <strong>B25 ≈ 93.36 jam</strong>
        </p>

        <p class="ref">
            Referensi:
            Accendo Reliability – B10 Life for Weibull Distribution
            (https://accendoreliability.com/b10-life-for-weibull-and-lognormal-distributions/)
        </p>
    </div>


    <div class="step">
        <h2>Langkah 10: Hitung MTTF (Dua Pendekatan)</h2>

        <p>
            Mean Time To Failure (MTTF) dapat dihitung dengan dua pendekatan yang berbeda,
            masing-masing memiliki tujuan dan interpretasi yang berbeda pula.
        </p>

        <h3>10.1 MTTF Empiris (Rata-rata Data Aktual)</h3>

        <p>
            MTTF empiris dihitung langsung dari rata-rata data Time To Failure (TTF)
            yang diamati di lapangan.
            Pendekatan ini <strong>tidak bergantung pada model statistik</strong>.
        </p>

        <p class="formula">
            MTTF<sub>empiris</sub> = ( Σ TTF ) / n
        </p>

        <table>
            <tr>
                <th>Parameter</th>
                <th>Nilai</th>
            </tr>
            <tr>
                <td>Jumlah data (n)</td>
                <td>5</td>
            </tr>
            <tr>
                <td>Σ TTF</td>
                <td>5049</td>
            </tr>
        </table>

        <p>
            <strong>MTTF empiris ≈ 1009.8 jam</strong>
        </p>

        <p class="ref">
            Catatan: Nilai ini merepresentasikan kondisi historis aktual dan sangat
            dipengaruhi oleh ukuran sampel.
        </p>

        <hr>

        <h3>10.2 MTTF Weibull (Menggunakan Fungsi Gamma)</h3>

        <p>
            Untuk distribusi Weibull dua parameter, nilai MTTF teoritis dihitung
            menggunakan fungsi Gamma.
            Pendekatan ini merepresentasikan <strong>mean populasi berdasarkan model Weibull</strong>.
        </p>

        <p class="formula">
            MTTF<sub>Weibull</sub> = η × Γ(1 + 1/β)
        </p>

        <table>
            <tr>
                <th>Parameter</th>
                <th>Nilai</th>
            </tr>
            <tr>
                <td>β</td>
                <td>0.4977459282</td>
            </tr>
            <tr>
                <td>η</td>
                <td>≈ 1140</td>
            </tr>
        </table>

        <p class="formula">
            MTTF = 1140 × Γ(1 + 1 / 0.4977459282)
        </p>

        <p>
            <strong>MTTF Weibull (Gamma) ≈ 2300.94 jam</strong>
        </p>

        <p class="ref">
            Referensi MTTF Weibull Isograph:
            file:///C:/ProgramData/Isograph/Reliability%20Workbench/15.0/eHelp/index.htm#t=Weibull_Distributions.htm&rhsearch=Weibull%20Analysis%20Options&rhhlterm=weibull%20option
        </p>

        <hr>

        <h3>Catatan Interpretasi Penting</h3>

        <ul>
            <li>
                Nilai <strong>β &lt; 1</strong> menunjukkan fase <em>Infant Mortality</em>,
                sehingga MTTF Weibull (Gamma) cenderung <strong>lebih besar</strong>
                dibandingkan rata-rata empiris.
            </li>
            <li>
                Untuk keperluan <strong>maintenance dan replacement policy</strong>,
                parameter <strong>B10 / B25 lebih relevan</strong> dibandingkan MTTF.
            </li>
            <li>
                Disarankan untuk <strong>menampilkan kedua nilai MTTF</strong>
                agar pengguna dapat membandingkan kondisi aktual dan model statistik.
            </li>
            <li>
                Perhitungan nyata dapat dicek di link berikut:
                <a href="https://docs.google.com/spreadsheets/d/10El80bPO7XHfL62RA4MIIqxfk6xEWqXzWILBJlByf-U/edit?usp=sharing"
                    target="_blank">Spreadsheet Contoh</a>
            </li>
        </ul>
    </div>


</body>

</html>
