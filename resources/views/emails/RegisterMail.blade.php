<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
  <div style="text-align: center; padding: 20px; border: 1px solid #ddd; margin: 0 auto; width: 500px;">
    <h1 style="color: #444;">회원가입을 위해 메일을 인증해 주세요.</h1>
    <p style="color: #666;">안녕하세요, MEDB입니다.</p>
    <p style="color: #666;">아래의 인증 링크를 클릭하셔서 회원가입을 완료해 주세요.</p>
    <a href="{{ $details['body'] }}" 
       style="display:inline-block; text-decoration:none; background-color:#3490dc; color:#fff;
              padding:10px 20px;margin-top:10px;border-radius:3px;font-weight:bold;">
        이메일 인증하기
    </a>
    <p style="color:#666;margin-top:30px;">감사합니다.</p>
</div>
</body>
</html>