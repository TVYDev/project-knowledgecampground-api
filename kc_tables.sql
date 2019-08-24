-------------------------Current Database Structure---------------------
CREATE TABLE users (
  id SERIAL NOT NULL,
  name VARCHAR(20) NOT NULL,
  email VARCHAR(30) NOT NULL,
  email_verified_at TIMESTAMP(0),
  password VARCHAR(255) NOT NULL,
  remember_token VARCHAR(100),
  created_at TIMESTAMP(0),
  updated_at TIMESTAMP(0),
  password1 VARCHAR(255),
  password2 VARCHAR(255),
  password3 VARCHAR(255),
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (id),
  UNIQUE (email)
);

CREATE TABLE user_avatars
(
  id SERIAL NOT NULL,
  user__id int4 NOT NULL,
  seed int4 NOT NULL,
  default_avatar_url VARCHAR(500) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
  img_url VARCHAR(500) NULL,
  created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  PRIMARY KEY (id),
  FOREIGN KEY (user__id) REFERENCES users(id),
  UNIQUE (seed)
);

CREATE TABLE questions
(
    id SERIAL NOT NULL,
    public_id VARCHAR(500) NOT NULL,
    title VARCHAR(1000) NOT NULL,
    user__id int4 NOT NULL,
    is_draft BOOLEAN NOT NULL DEFAULT FALSE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    posted_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (user__id) REFERENCES users(id),
    UNIQUE (public_id)
);

CREATE TABLE question_descriptions
(
    id SERIAL NOT NULL,
    question__id int4 NOT NULL,
    data VARCHAR NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (question__id) REFERENCES questions(id)
);

CREATE TABLE third_party_api_urls
(
  id SERIAL NOT NULL,
  key VARCHAR(500) NOT NULL,
  value VARCHAR(500) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT true,
  description VARCHAR(500) NOT NULL,
  created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
  PRIMARY KEY (id)
);
INSERT INTO third_party_api_urls(key,value,description) VALUES('jdenticon','https://avatars.dicebear.com/v2/jdenticon/{{PLACEHOLDER}}.svg','Open source library for generating identicons (avatar profile). Have placeholder as seed(=random string)');

CREATE TABLE system_messages
(
    id SERIAL NOT NULL,
    code VARCHAR(100) NOT NULL,
    message_sys VARCHAR(500),
    message_en VARCHAR(500),
    message_kh VARCHAR(500),
    type VARCHAR(50) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    description VARCHAR(500),
    created_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP(0) NOT NULL DEFAULT NOW(),
    PRIMARY KEY (id),
    UNIQUE (code)
);
---Success
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__USER_REGISTER','User is created successfully','Welcome','សូមស្វាគមន៍','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__USER_CHANGE_PASSWORD','Password is changed successfully','Password is changed successfully.','ពាក្យសម្ងាត់ត្រូវបានប្តូរថ្មីដោយជោគជ័យ','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__USER_LOGIN','User logs in successfully','Welcome again','សូមស្វាគមន៍ជាថ្មីម្ដងទៀត','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__USER_LOGOUT','User logs out successfully','See you next time','ជួបគ្នាលើកក្រោយទៀត','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__USER_IS_AUTHENTICATED','User is authenticated','User is authenticated','គណនីនេះត្រឹមត្រូវ','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__QUESTION_SAVE','Question is saved successfully','Your question is posted successfully','សំណួររបស់អ្នកបានបង្ហោះជាសាធារណៈដោយជោគជ័យ','info');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_SUCCESS__QUESTION_SAVE_DRAFT','Question is saved as draft successfully','Your drafted question is saved successfully','ពង្រាងនៃសំណួររបស់អ្នកបានរក្សាទុកដោយជោគជ័យ','info');
---Error
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__INVALID_TOKEN','Invalid Token','Please login again','សូមចូលភ្ជាប់គណនីម្ដងទៀត','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__EXPIRED_TOKEN','Expired Token','Session expires, please login again','គណនីអស់សុពលភាព សូមចូលភ្ជាប់គណនីម្ដងទៀត','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__UNAUTHENTICATED_USER','Unauthenticated user','Please login again','សូមចូលភ្ជាប់គណនីម្ដងទៀត','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__UNAUTHORIZED_ACCESS','Unauthorized Access','You do not have permission to access this function','អ្នកមិនមានសិទ្ធិចូលប្រើប្រាស់មុខងារនេះទេ','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__TOKEN_BLACKLISTED','Token blacklisted','You are not authenticated to access this','អ្នកមិនមានគណនីក្នុងប្រព័ន្ធកម្មវិធីនេះទេ','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__INTERNAL_SERVER_ERROR','Internal Server Error','Our server is sleeping, please try again in 5 minutes','ប្រព័ន្ធកម្មវិធីរបស់យើងកំពុងសម្រាក សូមព្យាយាមម្តងទៀតនៅ៥នាទីក្រោយ','error');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__EMAIL_OR_PASSWORD_INCORRECT','Email or password is incorrect','Your email or password is incorrect','អ៊ីមែល ឬពាក្យសម្ងាត់របស់អ្នកមិនត្រឹមត្រូវទេ','error');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__CURRENT_PASSWORD_NOT_CORRECT','The current password is not correct','Your current password is not correct','ពាក្យសម្ងាត់បច្ចុប្បន្នរបស់អ្នកមិនត្រឹមត្រូវទេ','error');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_ERROR__NEW_PASSWORD_SAME_LAST_THREE','Your new password must not be the same as your last 3 passwords','Your new password must not be the same as your last 3 passwords','ពាក្យសម្ងាត់ថ្មីមិនអាចដូចនឹងពាក្យសម្ងាត់ចាស់ពីមុន៣របស់អ្នកទេ','error');
---Validation
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NAME_REQUIRED','Name is required','Please provide your name','សូមបញ្ចូលឈ្មោះរបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NAME_STRING','Name must be a string','Please enter a valid name','សូមបញ្ចូលឈ្មោះឲ្យបានត្រឹមត្រូវ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NAME_MAX_50','Name must not exceed 50 characters','Please shorten your name to less than 50 characters','អ្នកបានចុះឈ្មោះជាមួយអ៊ីមែលនេះរួចហើយ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__EMAIL_REQUIRED','Email is required','Please provide your email','សូមបញ្ចូលអ៊ីមែលរបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__EMAIL_EMAIL','The email is not in correct format','Please enter a valid email address','សូមបញ្ចូលអ៊ីមែលឲ្យបានត្រឹមត្រូវ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__EMAIL_UNIQUE_USERS_EMAIL','The email has already existed in the system','You have already registered with this email','អ្នកបានចុះឈ្មោះជាមួយអ៊ីមែលនេះរួចហើយ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__PASSWORD_REQUIRED','Password is required','Please provide a password','សូមបញ្ចូលពាក្យសម្ងាត់','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__PASSWORD_MIN_8','Password must have at least 8 characters','Password must have at least 8 characters','ពាក្យសម្ងាត់ត្រូវមានយ៉ាងតិច៨តួអក្សរ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__CURRENT_PASSWORD_REQUIRED','Current password is required','Please provide your current password','សូមបញ្ចូលពាក្យសម្ងាត់បច្ចុប្បន្នរបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NEW_PASSWORD_REQUIRED','New password is required','Please provide your new password','សូមបញ្ចូលពាក្យសម្ងាត់ថ្មីរបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NEW_PASSWORD_MIN_8','New password must have at least 8 characters','Your new password must have at least 8 characters','ពាក្យសម្ងាត់ថ្មីរបស់អ្នកត្រូវមានយ៉ាងតិច៨តួអក្សរ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NEW_PASSWORD_DIFFERENT_CURRENT_PASSWORD','Current and new password must not be the same','Your new password must not be the same as current password','ពាក្យសម្ងាត់បច្ចុប្បន្ន និងថ្មីរបស់អ្នកមិនអាចដូចគ្នាបានទេ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__NEW_PASSWORD_CONFIRMED','Confirmed new password does not match','Confirmed new password does not match','ពាក្យសម្ងាត់ថ្មី និងការបញ្ជាក់ពាក្យសម្ងាត់ថ្មីមិនត្រូវគ្នាទេ','warning');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__TITLE_REQUIRED','Title is required','Please provide a title​ for your question','សូមបញ្ចូលចំណងជើងនៃសំណួររបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__TITLE_STRING','Title must be a string','Title is not a valid string','ចំណងជើងមានទម្រង់មិនត្រឹមត្រូវទេ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__TITLE_MAX_250','Title must not exceed 250 characters','Please shorten your title to less than 250 characters','សូមសម្រួលចំណងជើងនៃសំណួររបស់អ្នកឲ្យនៅតិចជាង២៥០តួអក្សរ','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__DESCRIPTION_REQUIRED','Description is required','Please provide description for your question','សូមបញ្ចូលការពិព័រនានៃសំណួររបស់អ្នក','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__DESCRIPTION_STRING','Description must be a string','Description is not a valid string','ការពិព័រនាមានទម្រង់មិនត្រឹមត្រូវទេ','warning');

INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__PUBLIC_ID_REQUIRED','public_id is required','Please provide a public id','','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__PUBLIC_ID_STRING','public_id must be a string','Public id is not a valid string','','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__IS_DRAFT_REQUIRED','is_draft is required','Please specify if it is a draft','','warning');
INSERT INTO system_messages(code,message_sys,message_en,message_kh,type) VALUES('KC_MSG_INVALID__IS_DRAFT_BOOLEAN','is_draft must be a boolean','Value to specify for a draft must be true or false','','warning');
------------------------------------------------------------------------
