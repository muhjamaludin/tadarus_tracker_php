async function getDataHijri() {
  let datetime = document.getElementById("datetime").value || "";
  let [result, hijri_date] = ["", ""];
  let [tanggal, _] = datetime.split("T");

  tanggal = swapDateFormat(tanggal);
  const res = await fetch(`http://api.aladhan.com/v1/gToH/${tanggal}`);
  if (res.status == 200) {
    result = await res.json();
    hijri_date = await result.data.hijri;
    hijri_date = swapDateFormat(hijri_date.date);

    document.getElementById("hijri").value = hijri_date;
  }
}

function swapDateFormat(tanggal = "2023-01-01") {
  const splitTanggal = tanggal.split("-");
  const reArrangeTanggal = [splitTanggal[2], splitTanggal[1], splitTanggal[0]];
  const joinTanggal = reArrangeTanggal.join("-");
  return joinTanggal;
}

const listHijriMonths = {
  "01": "Muharram",
  "02": "Safar",
  "03": "Rabi'ul Awal",
  "04": "Rabi'ul Akhir",
  "05": "Jumadil Awal",
  "06": "Jumadil Akhir",
  "07": "Rajab",
  "08": "Sya'ban",
  "09": "Ramadhan",
  10: "Syawwal",
  11: "Zulkaidah",
  12: "Zulhijjah",
};

const listHijriDays = {
  Sunday: "Ahad",
  Monday: "Isnaini",
  Tuesday: "Tsulaatsaai",
  Wednesday: "Arbi'aai",
  Thursday: "Khomiis",
  Friday: "Jumu'ati",
  Saturday: "Sabtu",
};

function readDateDetail() {
  const dateGregorian = document.getElementById("tanggal").textContent;
  const dateHijri = document.getElementById("hijri").textContent;

  const daysHijri = listHijriDays[dateGregorian.split(",")[0]];
  const hijriSwap = swapDateFormat(dateHijri).split("-");

  const resultDateHijriDetail = `${hijriSwap[0]} ${
    listHijriMonths[hijriSwap[1]]
  } ${hijriSwap[2]}`;

  document.getElementById(
    "hijri"
  ).textContent = `${daysHijri}, ${resultDateHijriDetail} H`;
}
