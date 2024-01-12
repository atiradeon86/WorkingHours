package com.bryan.bryanworkinghours

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.LinearLayout
import androidx.fragment.app.Fragment
import com.bryan.bryanworkinghours.databinding.FragmentFirstBinding


/**
 * A simple [Fragment] subclass as the default destination in the navigation.
 */
class FirstFragment : Fragment() {

    private var _binding: FragmentFirstBinding? = null

    // This property is only valid between onCreateView and
    // onDestroyView.
    private val binding get() = _binding!!

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {

        _binding = FragmentFirstBinding.inflate(inflater, container, false)

        return binding.root


    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        val myWebView: WebView = view.findViewById(R.id.webview)



        myWebView?.let {
            // Example: Set the height to 500 pixels
            val newHeightInPixels = 2090

            // Adjust the layout parameters of the WebView
            val params = it.layoutParams as LinearLayout.LayoutParams
            params.height = newHeightInPixels
            it.layoutParams = params

            // Load a URL into the WebView (replace with your desired URL)
            it.settings.javaScriptEnabled = true
            it.loadUrl("https://www.example.com")
        }



        myWebView.loadUrl("https://apps.bryan86.hu")
        myWebView.getSettings().setJavaScriptEnabled(true);
        myWebView.setWebViewClient(WebViewClient())
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}