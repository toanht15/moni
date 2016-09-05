SELECT id Id, to_address ToAddress, cc_address CcAddress, bcc_address BccAddress, subject Subject, body_plain BodyPlain, body_html BodyHTML, from_address FromAddress, envelope Envelope
  FROM mail_queues
  WHERE del_flg = 0 AND send_schedule <= NOW() LIMIT 10000