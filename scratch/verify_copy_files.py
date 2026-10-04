import os, shutil, sys
sys.stdout.reconfigure(encoding='utf-8')

src_dir = r"C:\Users\elnou\Downloads\New folder"
dest_dir = r"C:\Users\elnou\.gemini\antigravity\scratch\ecommerce-store\public\uploads\products"
backup_dir = r"C:\Users\elnou\.gemini\antigravity\scratch\ecommerce-store\backups\products_images"

os.makedirs(dest_dir, exist_ok=True)
os.makedirs(backup_dir, exist_ok=True)

file_mappings = [
    # New products
    ("Snapchat Plus abonnement exceptionnel.png", "prod_snapchat_plus.png"),
    ("Publicité futuriste pour Gemini 3.1.png", "prod_gemini_3_1_ai_pro.png"),
    ("ChatGPT Image 16 juil. 2026, 15_53_40.png", "prod_google_ai_pro_veo.png"),
    ("ChatGPT Image 10 juil. 2026, 17_57_38.png", "prod_chatgpt_plus.png"),
    ("ChatGPT Image 16 juil. 2026, 17_29_24.png", "prod_deezer_premium.png"),
    ("ChatGPT Image 18 juil. 2026, 18_35_29.png", "prod_nordvpn_3m.png"),
    ("ChatGPT Image 14 juil. 2026, 19_18_03.png", "prod_promax_iptv_12m.png"),
    ("ChatGPT Image 10 juil. 2026, 18_06_19.png", "prod_ea_fc_26_pc_steam.png"),
    ("Publicité vibrante pour eFootball PES 2026.png", "prod_pes_2026_gold_android.png"),
    ("ChatGPT Image 14 juil. 2026, 21_01_29.png", "prod_pes_2025_ios.png"),
    ("النسخة الكاملة من لعبة القراصنة.png", "prod_ac4_black_flag_resynced.png"),
    ("ChatGPT Image 14 juil. 2026, 19_21_36.png", "prod_ac4_black_flag_resynced_deluxe.png"),
    ("Affiche Adobe Creative Cloud colorée en arabe.png", "prod_adobe_creative_cloud_3m.png"),
    ("ChatGPT Image 28 juil. 2026, 12_48_29.png", "prod_xbox_game_pass_switch.png"),
    # Enhanced existing products
    ("Offre Netflix 4K Premium.png", "prod_netflix_4k_premium.png"),
    ("Affiche promotionnelle des écrans avec prix.png", "prod_netflix_screens_pricing.png"),
    ("ChatGPT Image 16 juil. 2026, 17_01_55.png", "prod_spotify_premium.png"),
    ("XBOX Game Pass Ultimate promotion.png", "prod_xbox_game_pass_ultimate.png"),
    ("Xbox Game Pass Ultimate promotion(2).png", "prod_xbox_game_pass_ultimate_features.png"),
    ("عرض ألفا_ اشتراك Adobe الإبداعي.png", "prod_adobe_creative_cloud_1year_poster.png"),
    ("ChatGPT Image 21 juil. 2026, 17_20_21.png", "prod_adobe_creative_cloud_365d.png"),
    ("Affiche promotionnelle Steam néonʻani.png", "prod_steam_120_games.png"),
    ("ChatGPT Image 22 août 2026, 12_51_47.png", "prod_steam_45k_games.png"),
    ("ألفا_ أكبر باقة ألعاب PC.png", "prod_steam_4400_games.png"),
]

print("Checking source files...")
missing = []
for src_name, dest_name in file_mappings:
    src_path = os.path.join(src_dir, src_name)
    if not os.path.isfile(src_path):
        print(f"MISSING: {src_name}")
        missing.append(src_name)
    else:
        sz = os.path.getsize(src_path)
        print(f"FOUND ({sz:,} bytes): {src_name} -> {dest_name}")

if missing:
    print(f"\nERROR: {len(missing)} files missing!")
else:
    print(f"\nSUCCESS: All {len(file_mappings)} files verified!")
