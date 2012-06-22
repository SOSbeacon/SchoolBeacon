package cnc.sosbeacon.view;

import android.content.Context;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.ColorFilter;
import android.graphics.LinearGradient;
import android.graphics.Paint;
import android.graphics.Rect;
import android.graphics.RectF;
import android.graphics.Shader.TileMode;
import android.graphics.drawable.Drawable;
import android.util.AttributeSet;
import android.util.Log;
import android.view.MotionEvent;
import android.widget.AdapterView;
import android.widget.ListView;
import cnc.sosbeacon.R;

public class RoundedRectListView extends ListView {	
	private Context mContext;

	public RoundedRectListView(Context context) {
		super(context);		
		this.mContext=context;
		init();
	}

	public RoundedRectListView(Context context, AttributeSet attrs) {
		super(context, attrs);
		this.mContext=context;
		init();
	}

	public RoundedRectListView(Context context, AttributeSet attrs, int defStyle) {
		super(context, attrs, defStyle);
		this.mContext=context;
		init();
	}

	protected void init(){
		setBackgroundDrawable(mContext.getResources().getDrawable(R.drawable.background_list_round));
		setCacheColorHint(Color.WHITE);
		setFooterDividersEnabled(false);	
		setSelector(new Selector(RoundedRectListView.this,-1));
	}

    @Override
	public boolean onInterceptTouchEvent(MotionEvent ev) {

		switch (ev.getAction()) {
		case MotionEvent.ACTION_DOWN:
			int x = (int) ev.getX();
			int y = (int) ev.getY();
			int itemnum = pointToPosition(x, y);

			if (itemnum == AdapterView.INVALID_POSITION) 
				break;			
			else
			{		
			   setSelector(new Selector(RoundedRectListView.this,itemnum));			
			}

			break;
		case MotionEvent.ACTION_UP:
			break;
		}
		return true;	
	}
    
    class Selector extends Drawable {
        private static final String TAG = "Selector";
        private Paint mPaint;
		@SuppressWarnings("unchecked")
		private AdapterView mList;
        private RectF mRectF;
        private int position;
        
        @SuppressWarnings("unchecked")
		public Selector(AdapterView list,int position) {
            mList = list;
            mPaint = new Paint();
            mRectF = new RectF();
            this.position=position;
            
            LinearGradient g=new LinearGradient(mRectF.top,mRectF.left,mRectF.right,mRectF.bottom,Color.parseColor("#058cf5"),Color.parseColor("#015fe6"),TileMode.REPEAT);        	        	
        	mPaint.setShader(g);
        }

        @Override
        public void draw(Canvas canvas) {
            Rect b = getBounds();
            int mPosition = mList.getSelectedItemPosition();
            if(mPosition==-1){
            	mPosition=position;
            }
            Log.d(TAG, "Position :" + mPosition);
            canvas.save();
            canvas.clipRect(b.left, b.top, b.right, (b.bottom + b.top) / 2);
            drawHalf(canvas, b, mPosition == 0);
            canvas.restore();
            canvas.save();
            canvas.clipRect(b.left, (b.bottom + b.top) / 2, b.right, b.bottom);
            drawHalf(canvas, b, mPosition == mList.getAdapter().getCount() - 1 && b.bottom == mList.getHeight());
            canvas.restore();
            Log.d(TAG, "draw " + b);
        }

        private void drawHalf(Canvas canvas, Rect b ,boolean round) {         	
        	
            if (round) {
                mRectF.set(b);
                canvas.drawRoundRect(mRectF, 10, 10, mPaint);               
            } else {
                canvas.drawRect(b, mPaint);               
            }
        }

        @Override
        public int getOpacity() {
            return 0;
        }

        @Override
        public void setAlpha(int alpha) {
        }

        @Override
        public void setColorFilter(ColorFilter cf) {
        }
    }
}


