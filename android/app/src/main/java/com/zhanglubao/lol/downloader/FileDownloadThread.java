package com.zhanglubao.lol.downloader;import android.app.NotificationManager;import android.content.Context;import com.zhanglubao.lol.config.ZLBConfiguration;import com.zhanglubao.lol.downloader.util.ConfigUtil;import com.zhanglubao.lol.downloader.util.Logger;import com.zhanglubao.lol.downloader.util.SDCardManager;import com.zhanglubao.lol.util.PlayerUtil;import com.zhanglubao.lol.util.Util;import java.io.BufferedInputStream;import java.io.BufferedOutputStream;import java.io.File;import java.io.FileNotFoundException;import java.io.FileOutputStream;import java.io.IOException;import java.io.InputStream;import java.net.HttpURLConnection;import java.net.SocketException;import java.net.SocketTimeoutException;import java.net.URL;public final class FileDownloadThread extends Thread {    private static final String TAG = "Download_Thread";    private static final int BUFFER_SIZE = 1024 * 4;// 缓冲大小    private DownloadInfo info;    private DownloadServiceManager download;    /**     * 重试次数     */    private int retryCount = 0;    private int retryInfoCount = 0;    private boolean cancel = false;    private boolean pause = false;    public FileDownloadThread(DownloadInfo di) {        super("FileDownloadThread");        info = di;        download = DownloadServiceManager.getInstance();        Logger.d("DownloadFlow", "FileDownloadThread: download_info: " + di);    }    public void pause() {        pause = true;    }    public void goOn() {        pause = false;    }    public void cancel() {        cancel = true;        pause = false;    }    public boolean isStop() {        return cancel;    }    public String getTaskId() {        if (info != null) {            return info.taskId;        }        return null;    }    @Override    public void run() {        super.run();        Logger.d("DownloadFlow", "FileDownloadThread: run()");        Logger.d(TAG, "FileDownloadThread start run");        info.setState(DownloadInfo.STATE_DOWNLOADING);        Logger.d(TAG, info.toString());        // 检查info文件完整性        if (info.segCount == 0) {            if (refreshData(info) == false) {// 重新取数据                return;            }        }        while (cancel == false && info.segId <= info.segCount                && info.getState() != DownloadInfo.STATE_FINISH                && info.getState() != DownloadInfo.STATE_CANCEL) {            if (downloadSegment(info) == false)                break;            if (info.segId == info.segCount) {                //if (info.segsSize[info.segId - 1] <= info.segDownloadedSize) {                // 全部下载完成                cancel = true;                download.getDownloadingData().remove(info.taskId);                info.setState(DownloadInfo.STATE_FINISH);                info.segUrl = null;                break;                //}            } else {                // 缓存下一个分片                info.segId += 1;                info.segDownloadedSize = 0;            }            info.segUrl = null;        }        cancel = true;    }    /**     * 下载分片     *     * @param info     * @return true下载完成；false下载失败或取消下载     */    private boolean downloadSegment(DownloadInfo info) {        Logger.d("DownloadFlow", "FileDownloadThread: downloadSegment()");        File f = checkAndGetFile(info);        if (f == null) {            cancel = true;            info.setState(DownloadInfo.STATE_EXCEPTION);            return false;        }        //final long endPosition = info.segsSize[info.segId - 1];// 当前分片的结束位置        long curPosition = info.segDownloadedSize;// 当前分片的下载进度位置//        if (curPosition >= endPosition) {//            // 当前分片已下载完成//            return true;//        }        InputStream is = getInputStreamFromURL(info);        if (is == null) {            cancel = true;            info.setState(DownloadInfo.STATE_EXCEPTION);            return false;        }        BufferedInputStream bis = null;        BufferedOutputStream bos = null;        try {            bis = new BufferedInputStream(is);            bos = new BufferedOutputStream(new FileOutputStream(f, true));            int len = 0;            byte[] buf = new byte[BUFFER_SIZE];            while (cancel == false                    && info.getState() == DownloadInfo.STATE_DOWNLOADING                    && (len = bis.read(buf, 0, BUFFER_SIZE)) != -1                    && cancel == false                    && info.getState() == DownloadInfo.STATE_DOWNLOADING) {// 因为read（）是耗时操作，所以需要二次判断                bos.write(buf, 0, len);                curPosition += len;                info.segDownloadedSize = curPosition;                info.downloadedSize += len;                info.setProgress();                if (info.retry != 0)                    info.retry = 0;                while (pause) {                    try {                        sleep(500L);                    } catch (InterruptedException e) {                    }                }            }            return true;        } catch (SocketTimeoutException e) {            Logger.e("DownloadFlow", "FileDownloadThread: downloadSegment(): " + e.toString());            Logger.e(TAG, e);            if (info.getState() != DownloadInfo.STATE_PAUSE                    && info.getState() != DownloadInfo.STATE_CANCEL) {                if (Util.hasInternet()) {                    info.setExceptionId(DownloadInfo.EXCEPTION_TIMEOUT);                    if (info.retry == 0) {                        PlayerUtil.showTips(info.getExceptionInfo());                    }                } else {                    info.setExceptionId(DownloadInfo.EXCEPTION_NO_NETWORK);                }                cancel = true;                info.setState(DownloadInfo.STATE_EXCEPTION);            }        } catch (SocketException e) {            Logger.e("DownloadFlow", "FileDownloadThread: downloadSegment(): " + e.toString());            Logger.e(TAG, e);            if (info.getState() != DownloadInfo.STATE_PAUSE                    && info.getState() != DownloadInfo.STATE_CANCEL) {                if (Util.hasInternet()) {                    info.setExceptionId(DownloadInfo.EXCEPTION_TIMEOUT);                    if (info.retry == 0) {                        PlayerUtil.showTips(info.getExceptionInfo());                    }                } else {                    info.setExceptionId(DownloadInfo.EXCEPTION_NO_NETWORK);                }                cancel = true;                info.setState(DownloadInfo.STATE_EXCEPTION);            }        } catch (FileNotFoundException e) {// SD卡被拔出            Logger.e("DownloadFlow", "FileDownloadThread: downloadSegment(): " + e.toString());            Logger.e(TAG, e);            NotificationManager nm = (NotificationManager) ZLBConfiguration.context                    .getSystemService(Context.NOTIFICATION_SERVICE);            nm.cancel(IDownload.NOTIFY_ID);        } catch (IOException e) {            Logger.e("DownloadFlow", "FileDownloadThread: downloadSegment(): " + e.toString());            Logger.e(TAG, e);            if (info.getState() != DownloadInfo.STATE_PAUSE                    && info.getState() != DownloadInfo.STATE_CANCEL) {                String[] temp = info.savePath.split(ConfigUtil.getDownloadPath());                SDCardManager m = new SDCardManager(temp[0]);                if (!m.exist()) {                    info.setExceptionId(DownloadInfo.EXCEPTION_NO_SDCARD);                    PlayerUtil.showTips(info.getExceptionInfo());                } else if (m.getFreeSize() - info.size <= 0) {                    info.setExceptionId(DownloadInfo.EXCEPTION_NO_SPACE);                    PlayerUtil.showTips(info.getExceptionInfo());                }                cancel = true;                info.setState(DownloadInfo.STATE_EXCEPTION);            }        } finally {            try {                if (bos != null)                    bos.close();                if (bis != null)                    bis.close();                if (is != null)                    is.close();            } catch (IOException e) {            }        }        return false;    }    /**     * 检查文件&获得文件对象     *     * @param info     * @throws IOException     */    private File checkAndGetFile(DownloadInfo info) {        File f = new File(info.savePath + info.segId + "."                + DownloadInfo.FORMAT_POSTFIX[info.format]);        Logger.d("DownloadFlow", "FileDownloadThread: checkAndGetFile(): " + f.getName());        if (f.exists() && f.isFile()) {            long len = f.length();            if (info.segDownloadedSize != len) {                info.segDownloadedSize = len;                long size = len;                for (int i = 0; i < (info.segId - 1); i++) {                    size += info.segsSize[i];                }                info.downloadedSize = size;            } else if (info.segCount == 1 && info.downloadedSize != len) {                info.downloadedSize = len;            }        } else {            // 文件损坏，重新生成数据            if (f.isDirectory())                PlayerUtil.deleteFile(f);            try {                f.createNewFile();            } catch (IOException e) {                return null;            }            info.segDownloadedSize = 0;            long size = 0;            for (int i = 0; i < (info.segId - 1); i++) {                size += info.segsSize[i];            }            info.downloadedSize = size;        }        return f;    }    /**     * 根据URL得到输入流     *     * @param info     * @return     */    private InputStream getInputStreamFromURL(DownloadInfo info) {        Logger.d(TAG, "segId:" + info.segId);        String url = getUrl(info);        Logger.d("DownloadFlow", "FileDownloadThread: getInputStreamFromURL(): download_url: " + url);        Logger.d(TAG, "locationUrl:" + url);        if (url != null && url.length() != 0) {            HttpURLConnection con;            try {                con = (HttpURLConnection) new URL(url).openConnection();                con.setConnectTimeout(ConfigUtil.TIMEOUT);                con.setReadTimeout(ConfigUtil.TIMEOUT);                con.setAllowUserInteraction(true);                con.setRequestProperty("Range", "bytes="                        + info.segDownloadedSize + "-");                con.connect();                info.segsSize[info.segId - 1]=con.getContentLength();                int rcode = con.getResponseCode();                Logger.d(TAG, "responseCode:" + rcode);                // 如果所存的url地址不可用404，则重新获取url地址并重新从0%下载;403为CMCC网络不可用                if (rcode != HttpURLConnection.HTTP_NOT_FOUND                        && rcode != HttpURLConnection.HTTP_FORBIDDEN) {                    InputStream is = con.getInputStream();                    retryCount = 0;                    return is;                }            } catch (IOException e) {                Logger.e("DownloadFlow", "FileDownloadThread: getInputStreamFromURL(): error: " + e.toString());                Logger.e(TAG, "getInputStreamFromURL()", e);                if (retryCount < 1) {// 重试一次                    retryCount++;                    return getInputStreamFromURL(info);                }            }        }        retryCount = 0;        return null;    }    /**     * 获得下载地址     *     * @param info     * @return     */    private String getUrl(DownloadInfo info) {        Logger.d("DownloadFlow", "FileDownloadThread: getUrl()");        if (info.segsUrl == null                || info.segCount != info.segsUrl.length                || (System.currentTimeMillis() - info.getUrlTime) > 2.5 * 60 * 60 * 1000) {            if (refreshData(info) == false) {                return null;            }        }        String segUrl = info.segsUrl[info.segId - 1];        Logger.d("DownloadFlow", "FileDownloadThread: #0");        Logger.d("DownloadFlow", "FileDownloadThread: #1 : " + segUrl);        String url = null;        url = info.segsUrl[info.segId - 1];        info.segUrl = url;        return url;    }    /**     * 重新取接口数据     *     * @return 是否取成功     */    private boolean refreshData(DownloadInfo info) {        if (!DownloadUtils.getDownloadData(info)) {            info.setState(DownloadInfo.STATE_WAITING);            PlayerUtil.showTips(info.getExceptionInfo());            return false;        }        return true;    }}