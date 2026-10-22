function validateForm() {
    const studentId = document.getElementById('student_id').value.trim();
    const email = document.getElementById('email').value.trim();

    const categories = document.querySelectorAll('input[name="category[]"]:checked');

    if (categories.length === 0) {
        alert('請至少選擇一個報名組別。');
        return false;
    }

    const expectedEmail = 's' + studentId + '@mail.yzu.edu.tw';

    if (email !== expectedEmail) {
        alert('學校信箱必須與學號一致。\n例如學號 1151538，信箱應為 s1151538@mail.yzu.edu.tw');
        return false;
    }

    return true;
}
