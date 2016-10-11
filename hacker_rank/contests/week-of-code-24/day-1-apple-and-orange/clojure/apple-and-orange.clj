(use '[clojure.string :only (split triml)])

(let [[s t]   (map #(Integer/parseInt %) (split (read-line) #"\s+"))
      [a b]   (map #(Integer/parseInt %) (split (read-line) #"\s+"))
      [m n]   (map #(Integer/parseInt %) (split (read-line) #"\s+"))
      apples  (map #(Integer/parseInt %) (split (read-line) #"\s+"))
      oranges (map #(Integer/parseInt %) (split (read-line) #"\s+"))

      apples-on-house  (filter #(and (>= % s) (<= % t)) (map #(+ a %) apples))
      oranges-on-house (filter #(and (>= % s) (<= % t)) (map #(+ b %) oranges))]

  (println (count apples-on-house))
  (println (count oranges-on-house))
  )
