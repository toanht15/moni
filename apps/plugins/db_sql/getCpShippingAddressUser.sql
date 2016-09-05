select
	bur.no,
	su.last_name,
	su.first_name,
	su.last_name_kana,
	su.first_name_kana,
	concat(su.zip_code1, '-', su.zip_code2),
	p.name,
	su.address1,
	su.address2,
	su.address3,
	concat(su.tel_no1, '-', su.tel_no2, '-', su.tel_no3),
	su.updated_at
from
	shipping_address_users su
inner join
	cp_users cu
	on cu.id = su.cp_user_id
	and cu.cp_id = ?cp_id?
	and cu.user_id in (?user_ids?)
inner join
	brands_users_relations bur
	on bur.user_id = cu.user_id
	and bur.brand_id = ?brand_id?
	and bur.withdraw_flg = 0
	and bur.del_flg = 0
left join
  prefectures p
  on p.id = su.pref_id
where
	su.del_flg = 0
