from gtts import gTTS
import sys;
import os;

def generate_speech(text,output_path,lang='ar'):
    try:
        tts = gTTS(text=text, lang=lang, slow=False)
        audio_dir=os.path.dirname(output_path)
        if audio_dir and not os.path.exists(audio_dir):
            os.makedirs(audio_dir)
        tts.save(output_path)

    except Exception as e:
        sys.stderr.write(f"Error generating speech: {e}\n")


if __name__=="__main__":
    if len(sys.argv) > 2:
        text_to_speak=sys.argv[1]
        output_file_path=sys.argv[2]
        generate_speech(text_to_speak,output_file_path)
    else:
        sys.stderr.write("Error: Missing text or output path arguments.\n")
